<?php

return [
    'max_distance_km' => env('PAIRINGS_MAX_DISTANCE_KM', 500),
    'time_window_seconds' => env('PAIRINGS_TIME_WINDOW_SECONDS', 5),
    'az_tolerance_deg' => env('PAIRINGS_AZ_TOLERANCE', 5),
    'ev_tolerance_deg' => env('PAIRINGS_EV_TOLERANCE', 5),
    'fov_tolerance' => env('PAIRINGS_FOV_TOLERANCE', 1.0),
];
