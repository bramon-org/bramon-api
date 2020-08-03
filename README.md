# BRAMON - API

API da BRAMON


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

```http request
GET https://api.bramonmeteor.org/v1/admin/operators
Accept: */*
Cache-Control: no-cache
Authorization: Bearer XOXOXOXOXOXOXOXO
Content-Type: application/json
```
