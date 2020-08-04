## Operadores

#### Cadastro

```http request
POST https://api.bramonmeteor.org/v1/admin/operators
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "name": "Test Operator",
  "email": "test-operatorx@bramonmeteor.org",
  "mobile_phone": "+5511222223333",
  "role": "operator|admin|editor"
}
```

#### Atualização

```http request
PUT https://api.bramonmeteor.org/v1/admin/operators/bcacad8c-7707-48ed-8d65-2579ac2db24b
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "name": "Test Operator",
  "email": "test-operatorx@bramonmeteor.org",
  "mobile_phone": "+5511222223333",
  "role": "operator|admin|editor"
}
```

#### Listagem

```http request
GET https://api.bramonmeteor.org/v1/admin/operators
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

#### Visualizar detalhes

```http request
GET https://api.bramonmeteor.org/v1/admin/operators/bcacad8c-7707-48ed-8d65-2579ac2db24b
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```
