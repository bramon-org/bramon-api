<?php

namespace App\Filter\Capture;

use Fouladgar\EloquentBuilder\Support\Foundation\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

class StationFilter extends Filter
{
    /**
     * Apply the station condition to the query.
     *
     * @param Builder $builder
     * @param mixed   $value
     *
     * @return Builder
     */
    public function apply(Builder $builder, $value): Builder
    {
        return $builder
            ->join('stations', 'stations.id', '=', 'captures.station_id')
            ->where('stations.id', '=', $value);
    }
}
