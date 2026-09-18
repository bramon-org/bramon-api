#!/bin/bash

app_env=$APP_ENV

find /var/www/html -type d -exec chmod 755 {} +
find /var/www/html -type f -exec chmod 644 {} +
chmod -R 775 /var/www/html/storage

if [ $# -eq 0 ]; then
  php artisan migrate --force
  apache2-foreground
fi

exec "$@"
