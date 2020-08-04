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

## Mais detalhes

Você pode obter mais detalhes na [documentação](docs/README.md).
