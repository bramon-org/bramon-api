#### Estações

##### Cadastro

```http request
POST https://api.bramonmeteor.org/v1/admin/stations
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "user_id": "bcacad8c-7707-48ed-8d65-2579ac2db24b",
  "name": "Test User",
  "latitude": -27.938473,
  "longitude": -48.838273,
  "azimuth": 160,
  "elevation": 30,
  "fov": 52.23,
  "camera_model": "SAMSUNG SCB-2000",
  "camera_lens": "MegaPixel 4mm F1.0",
  "camera_capture": "AverMedia USB"
}
```

##### Atualização

```http request
PUT https://api.bramonmeteor.org/v1/admin/stations/bcacad8c-7707-48ed-8d65-2579ac2db24b
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "user_id": "bcacad8c-7707-48ed-8d65-2579ac2db24b",
  "name": "Test User",
  "latitude": -27.938473,
  "longitude": -48.838273,
  "azimuth": 160,
  "elevation": 30,
  "fov": 52.23,
  "camera_model": "SAMSUNG SCB-2000",
  "camera_lens": "MegaPixel 4mm F1.0",
  "camera_capture": "AverMedia USB"
}
```

##### Visualização

```http request
GET https://api.bramonmeteor.org/v1/admin/stations/bcacad8c-7707-48ed-8d65-2579ac2db24b
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

##### Listagem

```http request
GET https://api.bramonmeteor.org/v1/admin/stations
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

##### Listagem por operador

```http request
GET https://api.bramonmeteor.org/v1/admin/stations/bcacad8c-7707-48ed-8d65-2579ac2db24b/list
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```
