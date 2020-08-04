# BRAMON - API

API da BRAMON.

## Instalação Local

Construindo os containers na primeira vez

```console
docker-compose build
```

Iniciando os containers

```console
docker-compose up
```

Migrações

```console
docker-compose exec apache bash -c 'php artisan migrate'
```

## Autenticando

Todos os requests deverão conter no mínimo os cabeçalhos mencionados abaixo:

```
Authorization: Bearer XOXOXOXOXOXOXOXO
```

Onde `XOXOXOXOXOXOXOXO` é a chave de API do usuário.

## End-points de Admin

No namespace de Admin, somente usuários com `role` de administrador podem efetuar operações.

### Operadores

#### Cadastro

```http request
POST http://local-api.bramonmeteor.org/v1/admin/operators
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO

{
  "name": "Test Operator",
  "email": "test-operatorx@bramonmeteor.org",
  "mobile_phone": "+5511222223333",
  "role": "operator"
}
```

Valores possíveis para role: `operator`, `admin` & `editor`.

#### Listagem

```http request
GET http://local-api.bramonmeteor.org/v1/admin/operators
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

#### Estações

##### Cadastro

```http request

```

##### Listagem

```http request
GET http://local-api.bramonmeteor.org/v1/admin/stations
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

##### Listagem por operador

```http request
GET http://local-api.bramonmeteor.org/v1/admin/stations/bcacad8c-7707-48ed-8d65-2579ac2db24b/list
Accept: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```
