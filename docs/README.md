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
Exemplo: `https://api.bramonmeteor.org/v1/public/stations?filter[state]=SC&filter[city]=Florianópolis`

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

### Auto-creating stations from capture analyze files

To allow automatic creation of Station records when importing/processing capture analyze files (UFO A.XML), set the CAPTURE_AUTO_CREATE_USER_ID environment variable to a valid user UUID. The importer and UFO driver will use metadata parsed from the analyze XML to create a Station and associate the Capture to it. Newly created stations are created as visible=false by default so operators can review and adjust them.
