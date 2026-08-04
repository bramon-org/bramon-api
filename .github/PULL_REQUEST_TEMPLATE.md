---
title: "Add pairings route and PairingService"
labels: [enhancement, api]
assignees: []
---

This PR adds an endpoint to list probable pairings between captures. It implements the basic algorithm described in issue #4 and exposes it in admin, operator and public namespaces.

What I added
- app/Services/PairingService.php — core algorithm to find pairings based on:
  - only analyzed captures (class IS NOT NULL)
  - date/time filters (captured_date OR captured_from/captured_to)
  - time window between captures (query param time_window_seconds, default 5s)
  - geographic distance filtering (Haversine, max_distance_km param, default 500km)
  - optional refinement by azimuth, elevation and FOV with tolerances
- Controllers: Admin/Operator/Open PairingController (thin wrappers)
- Routes: GET /v1/{admin,operator,public}/pairings

Defaults used
- max_distance_km = 500
- time_window_seconds = 5
- az_tolerance_deg = 5
- ev_tolerance_deg = 5
- fov_tolerance = 1.0

Notes & next steps
- Current implementation loads captures in memory for the requested interval and compares them pairwise. This is simple and correct for small intervals but may be slow for large intervals; I recommend:
  - adding a bounding-box prefilter in SQL using latitude/longitude to reduce candidates
  - or precomputing pairings asynchronously when UFO Orbit output is uploaded
  - adding unit/integration tests and OpenAPI annotations

Linked issue: #4

