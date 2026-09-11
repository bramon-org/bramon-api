# PR Review checklist — Pairings feature

Por favor, revisores, verifiquem os itens abaixo ao revisar o PR #67:

- [ ] Verificar se a rota GET /v1/{admin,operator,public}/pairings está corretamente registrada e com middleware apropriado.
- [ ] Conferir validação de parâmetros (captured_date / captured_from / captured_to / max_distance_km / time_window_seconds / azimuth / elevation / fov).
- [ ] Revisar o algoritmo em app/Services/PairingService.php: garantir que apenas captures com class != null sejam consideradas.
- [ ] Conferir cálculo de distância Haversine e unidade (km).
- [ ] Verificar limites padrão (max_distance_km=500, time_window_seconds=5) e tolerâncias para az/ev/fov.
- [ ] Considerar performance: checar se há filtros suficientes para evitar carregar grandes volumes em memória.
- [ ] Testes: revisar novo teste básico em tests/Feature/PairingEndpointTest.php e sugerir casos adicionais.
- [ ] Segurança: garantir que endpoints admin/operator requerem autenticação apropriada.

