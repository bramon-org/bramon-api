FROM php:7.4-apache AS dev

WORKDIR /var/www/html/

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

RUN apt-get update && apt-get install -y git zip unzip \
    libmcrypt-dev libxml2-dev libzip-dev --no-install-recommends

RUN pecl install -o -f mcrypt \
    && pecl install -o -f zip

RUN docker-php-ext-enable mcrypt \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl

RUN docker-php-ext-enable xdebug

RUN echo "xdebug.mode=debug,develop" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=172.17.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini


COPY php.ini-development.txt $PHP_INI_DIR/php.ini

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN a2enmod headers rewrite negotiation

COPY . .

###

FROM php:7.4-apache AS artifact

RUN a2enmod headers
RUN rm $PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini

COPY php.ini-production.txt $PHP_INI_DIR/php.ini
COPY . .

ENTRYPOINT [ "./docker-entrypoint.sh"]
