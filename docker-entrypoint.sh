#!/bin/bash

app_env=$APP_ENV

if [ $# -eq 0 ]; then
  php artisan migrate --force
  apache2-foreground
fi

exec "$@"
