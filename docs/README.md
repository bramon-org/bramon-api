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
- [Operadores](operators/README.md)
- [Pública](public/README.md)
