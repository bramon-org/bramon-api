<?php

namespace App\Services;

use App\Models\Capture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PairingService
{
    /**
     * Find probable pairings between captures.
     *
     * This implementation uses a bounding-box prefilter to reduce candidate pairs:
     * For each capture A we query captures B that are within the time window and inside
     * a latitude/longitude box computed from max_distance_km around station A. Then
     * we compute the precise Haversine distance before accepting the pair.
     *
     * See method findPairings for supported params.
     */
    public function findPairings(array $params): array
    {
        $maxDistanceKm = isset($params['max_distance_km']) ? (float)$params['max_distance_km'] : 500.0;
        $timeWindow = isset($params['time_window_seconds']) ? (int)$params['time_window_seconds'] : 5;

        $az = isset($params['azimuth']) ? (float)$params['azimuth'] : null;
        $azTol = isset($params['az_tolerance_deg']) ? (float)$params['az_tolerance_deg'] : 5.0;

        $ev = isset($params['elevation']) ? (float)$params['elevation'] : null;
        $evTol = isset($params['ev_tolerance_deg']) ? (float)$params['ev_tolerance_deg'] : 5.0;

        $fov = isset($params['fov']) ? (float)$params['fov'] : null;
        $fovTol = isset($params['fov_tolerance']) ? (float)$params['fov_tolerance'] : 1.0;

        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 50;
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;

        // Determine time range
        $from = null;
        $to = null;

        if (!empty($params['captured_date'])) {
            $date = Carbon::createFromFormat('Y-m-d', $params['captured_date']);
            $from = $date->copy()->startOfDay();
            $to = $date->copy()->endOfDay();
        } else {
            if (!empty($params['captured_from'])) {
                $from = Carbon::parse($params['captured_from']);
            }
            if (!empty($params['captured_to'])) {
                $to = Carbon::parse($params['captured_to']);
            }
        }

        if ($from === null && $to === null) {
            // default to today
            $from = Carbon::now()->startOfDay();
            $to = Carbon::now()->endOfDay();
        }

        if ($from === null) {
            // if only to provided, set from to 24h before
            $from = $to->copy()->subDay();
        }
        if ($to === null) {
            $to = $from->copy()->endOfDay();
        }

        // Get candidate captures A (analyzed in the interval)
        $candidatesA = Capture::whereNotNull('class')
            ->whereBetween('captured_at', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->with('station')
            ->get();

        $results = [];

        foreach ($candidatesA as $a) {
            if (!$a->station || !$this->hasCoordinates($a->station)) {
                continue;
            }

            if (!$this->captureMatchesOptional($a, $az, $azTol, $ev, $evTol, $fov, $fovTol)) {
                continue;
            }

            // compute bounding box around station A for maxDistanceKm
            $bounds = $this->boundingBox((float)$a->station->latitude, (float)$a->station->longitude, $maxDistanceKm);

            // time window for B candidates
            $aTime = Carbon::parse($a->captured_at);
            $bFrom = $aTime->copy()->subSeconds($timeWindow)->toDateTimeString();
            $bTo = $aTime->copy()->addSeconds($timeWindow)->toDateTimeString();

            // Query B candidates using prefilters: time window, station coordinates inside bounding box, and analyzed
            $bCandidates = Capture::whereNotNull('class')
                ->where('id', '!=', $a->id)
                ->whereBetween('captured_at', [$bFrom, $bTo])
                ->whereHas('station', function ($q) use ($bounds) {
                    $q->whereBetween('latitude', [$bounds['min_lat'], $bounds['max_lat']])
                      ->whereBetween('longitude', [$bounds['min_lng'], $bounds['max_lng']]);
                })
                ->with('station')
                ->get();

            foreach ($bCandidates as $b) {
                if (!$b->station || !$this->hasCoordinates($b->station)) {
                    continue;
                }

                if ($a->station->id === $b->station->id) {
                    continue;
                }

                $timeDiff = abs(strtotime($a->captured_at) - strtotime($b->captured_at));
                if ($timeDiff > $timeWindow) {
                    continue;
                }

                $dist = $this->haversineDistance(
                    (float)$a->station->latitude,
                    (float)$a->station->longitude,
                    (float)$b->station->latitude,
                    (float)$b->station->longitude
                );

                if ($dist > $maxDistanceKm) {
                    continue;
                }

                if (!$this->captureMatchesOptional($b, $az, $azTol, $ev, $evTol, $fov, $fovTol)) {
                    continue;
                }

                $azDiff = null;
                $evDiff = null;
                $fovDiff = null;

                if (isset($a->az) && isset($b->az)) {
                    $azDiff = abs((float)$a->az - (float)$b->az);
                }
                if (isset($a->ev) && isset($b->ev)) {
                    $evDiff = abs((float)$a->ev - (float)$b->ev);
                }
                if (isset($a->fov) && isset($b->fov)) {
                    $fovDiff = abs((float)$a->fov - (float)$b->fov);
                }

                $results[] = [
                    'capture_a' => $a,
                    'capture_b' => $b,
                    'time_difference_seconds' => $timeDiff,
                    'distance_km' => round($dist, 3),
                    'azimuth_diff' => $azDiff,
                    'elevation_diff' => $evDiff,
                    'fov_diff' => $fovDiff,
                ];
            }
        }

        // simple pagination
        $total = count($results);
        $offset = ($page - 1) * $limit;
        $paged = array_slice($results, $offset, $limit);

        return [
            'total' => $total,
            'per_page' => $limit,
            'page' => $page,
            'data' => $paged,
        ];
    }

    private function hasCoordinates($station): bool
    {
        return isset($station->latitude) && isset($station->longitude) && $station->latitude !== null && $station->longitude !== null;
    }

    private function captureMatchesOptional($capture, $az, $azTol, $ev, $evTol, $fov, $fovTol): bool
    {
        if ($az !== null) {
            if (!isset($capture->az) || abs((float)$capture->az - $az) > $azTol) {
                return false;
            }
        }
        if ($ev !== null) {
            if (!isset($capture->ev) || abs((float)$capture->ev - $ev) > $evTol) {
                return false;
            }
        }
        if ($fov !== null) {
            if (!isset($capture->fov) || abs((float)$capture->fov - $fov) > $fovTol) {
                return false;
            }
        }

        return true;
    }

    /**
     * Haversine distance between two lat/lng points in kilometers.
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Compute a latitude/longitude bounding box that contains a circle of radius $distanceKm
     * around the point ($lat, $lng). Returns min_lat, max_lat, min_lng, max_lng.
     * Uses simple equirectangular approximations which are sufficient for modest distances.
     */
    private function boundingBox(float $lat, float $lng, float $distanceKm): array
    {
        // approximate degrees per km
        $degLat = $distanceKm / 110.574; // ~1 deg lat = 110.574 km
        $degLng = $distanceKm / (111.320 * cos(deg2rad($lat)) + 1e-12);

        return [
            'min_lat' => $lat - $degLat,
            'max_lat' => $lat + $degLat,
            'min_lng' => $lng - $degLng,
            'max_lng' => $lng + $degLng,
        ];
    }
}
