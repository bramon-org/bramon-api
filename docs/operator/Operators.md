## Operadores

#### Detalhes

```http request
GET https://api.bramonmeteor.org/v1/operator/operators
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

#### Atualização

```http request
PUT https://api.bramonmeteor.org/v1/operator/operators
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "name": "Test Operator",
  "mobile_phone": "+5511222223333",
  "city": "City",
  "state": "State"
}
```

#### Visualizar detalhes

```http request
GET https://api.bramonmeteor.org/v1/operator/operators/bcacad8c-7707-48ed-8d65-2579ac2db24b
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```
