# BRAMON - API

API da BRAMON.

## Autenticação

Todos os requests (com excessão do namespace **public**) deverão conter o cabeçalho de autorização e seu token de
operador como valor.

```
Authorization: Bearer XOXOXOXOXOXOXOXO
```

Onde `XOXOXOXOXOXOXOXO` é a chave de API do usuário.


## Namespaces

- [Admin](admin/README.md)
- [Operador](operator/README.md)
- [Pública](public/README.md)

## Filters

A API possui alguns filtros que podem ser utilizados em sua requisição GET, para
utilizar algum filtro, você adicionar na url: `filter[nome-do-filtro]=valor`.

Os filtros disponíveis estão separados por namespace/recurso:

### Captures

| Filtro        | Valores permitidos                |
|---------------|-----------------------------------|
| analyzed      | true, false                       |
| captured_at   | date (ex: 2020-01-06)             |
| class         | J8_PHP                            |
| interval      | dates (ex: 2020-01-06,2020-01-10) |
| station       | uuid                              |

### Stations

| Filtro        | Valores permitidos                |
|---------------|-----------------------------------|
| active        | true, false                       |
| city          | string (ex.: Florianópolis)       |
| country       | string (ex: Brasil)               |
| state         | string (ex: SC)                   |
| source        | UFO, RMS                          |
