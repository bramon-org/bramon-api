# BRAMON - API

![tests](https://github.com/bramon-org/bramon-api/workflows/tests/badge.svg)
[![Codacy Security Scan](https://github.com/bramon-org/bramon-api/actions/workflows/codacy-analysis.yml/badge.svg)](https://github.com/bramon-org/bramon-api/actions/workflows/codacy-analysis.yml)

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

Populando a base de testes

```console 
docker-compose exec apache bash -c 'php artisan db:seed'
```

Atualizando a documentação

```console
php artisan swagger-lume:generate
```

## Mais detalhes

Você pode obter mais detalhes nas documentações:

- [GitHub](docs/README.md)
- [Swagger](https://api.bramonmeteor.org/api/documentation)

