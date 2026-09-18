FROM php:8.4-apache-bookworm AS dev

WORKDIR /var/www/html/

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl git zip unzip libicu-dev libmcrypt-dev libxml2-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f mcrypt xdebug \
    && docker-php-ext-enable mcrypt xdebug

RUN docker-php-ext-install -j"$(nproc)" \
    pdo pdo_mysql zip opcache intl

RUN echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=172.17.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini


COPY php.ini-development.txt $PHP_INI_DIR/php.ini

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN a2enmod headers rewrite negotiation

COPY --chown=www-data:www-data . .

RUN find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod -R 775 /var/www/html/storage

###

FROM php:8.4-apache-bookworm AS artifact

RUN a2enmod headers
RUN rm -f $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY php.ini-production.txt $PHP_INI_DIR/php.ini
COPY --chown=www-data:www-data . .

RUN find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} + \
    && chmod -R 775 /var/www/html/storage

ENTRYPOINT [ "./docker-entrypoint.sh"]
