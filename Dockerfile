FROM php:7.4-apache AS dev

WORKDIR /var/www/html/

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN ln -sf /dev/stdout /var/log/apache2/access.log && \
    ln -sf /dev/stderr /var/log/apache2/error.log

RUN apt-get update && apt-get install -y libmcrypt-dev \
    libmagickwand-dev libpq-dev git zip unzip libxml2-dev \
    libzip-dev supervisor python-pip python-setuptools --no-install-recommends \
    && pecl install -o -f imagick \
    && pecl install -o -f mcrypt \
    && pecl install -o -f zip \
    && pecl install -o -f xdebug \
    && pecl install -o -f redis \
    && docker-php-ext-enable mcrypt \
    && docker-php-ext-enable imagick \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-install soap \
    && docker-php-ext-install sockets \
    && docker-php-ext-install opcache \
    && docker-php-ext-install intl \
    && docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN a2enmod headers rewrite negotiation

COPY . .

###

FROM php:7.4-apache AS artifact

RUN pecl install redis-5.1.1 \
    && docker-php-ext-enable redis

RUN a2enmod headers

COPY . .

ENTRYPOINT [ "./docker-entrypoint.sh"]
