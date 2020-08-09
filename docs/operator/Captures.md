#### Capturas

##### Cadastro

```http request
POST https://api.bramonmeteor.org/v1/operator/captures
Content-Type: multipart/form-data
Authorization: Bearer XOXOXOXOXOXOXOXO

station_id=bcacad8c-7707-48ed-8d65-2579ac2db24b
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5.avi
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5.txt
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5.xml
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5A.XML
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5M.bmp
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5P.jpg
files[]=/Volumes/external/BRAMON/estacoes/!data/TLP5/2020/202004/20200420/M20200420_220139_TLP_5T.jpg
```

##### Listagem

```http request
GET https://api.bramonmeteor.org/v1/operator/captures
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```

##### Visualização

```http request
GET https://api.bramonmeteor.org/v1/operator/captures/bcacad8c-7707-48ed-8d65-2579ac2db24b
Content-Type: application/json
Authorization: Bearer XOXOXOXOXOXOXOXO
```
