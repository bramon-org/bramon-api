# ==========================================
# 1. Base (Extensões necessárias para o Lumen)
# ==========================================
FROM php:8.4-apache-bookworm AS base

WORKDIR /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Habilita mod_rewrite para as rotas do Lumen funcionarem
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log \
    && a2enmod headers rewrite

# Instala dependências do sistema e extensões nativas do Lumen
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl git zip unzip libicu-dev libmcrypt-dev libxml2-dev libzip-dev \
    && pecl install -o -f mcrypt \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql zip opcache intl \
    && rm -rf /var/lib/apt/lists/*

# Copia o Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==========================================
# 2. Stage de Desenvolvimento
# ==========================================
FROM base AS dev

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=172.17.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini

COPY php.ini-development.txt $PHP_INI_DIR/php.ini

COPY --chown=www-data:www-data . .

# ==========================================
# 3. Stage de Produção (Artifact)
# ==========================================
FROM base AS artifact

COPY php.ini-production.txt $PHP_INI_DIR/php.ini

# Otimização do Composer (Aproveitando Cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copia a aplicação
COPY --chown=www-data:www-data . .

# Otimiza o autoloader da classe para APIs em Lumen
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

# Permissões de escrita apenas na pasta de logs e storage do Lumen
RUN chmod -R 775 storage \
    && chown -R www-data:www-data storage

ENTRYPOINT ["./docker-entrypoint.sh"]