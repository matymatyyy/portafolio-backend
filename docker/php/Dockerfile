FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    bash \
    git \
    icu-dev \
    libpq-dev \
    linux-headers \
    zip \
    unzip \
    $PHPIZE_DEPS

RUN docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_pgsql

RUN pecl install redis xdebug \
    && docker-php-ext-enable redis xdebug

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./

RUN composer install --no-scripts --no-autoloader --prefer-dist 2>/dev/null || true

COPY . .

RUN composer dump-autoload --optimize 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/html/app/var 2>/dev/null || true

EXPOSE 9000

CMD ["php-fpm"]
