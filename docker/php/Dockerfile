# =============================================================================
# Stage 1: Base image with shared dependencies
# =============================================================================
FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache \
    bash \
    icu-dev \
    libpq-dev \
    zip \
    unzip

RUN docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_pgsql

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# =============================================================================
# Stage 2: Development (includes xdebug, git, dev tools)
# =============================================================================
FROM base AS dev

RUN apk add --no-cache \
    git \
    linux-headers \
    $PHPIZE_DEPS

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY composer.json composer.lock* ./

RUN composer install --no-scripts --no-autoloader --prefer-dist 2>/dev/null || true

COPY . .

RUN composer dump-autoload --optimize 2>/dev/null || true

RUN chown -R www-data:www-data /var/www/html/app/var 2>/dev/null || true

EXPOSE 9000

CMD ["php-fpm"]

# =============================================================================
# Stage 3: Production (no xdebug, no dev dependencies, optimized)
# =============================================================================
FROM base AS prod

COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini

COPY composer.json composer.lock* ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --optimize-autoloader

COPY . .

RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

RUN chown -R www-data:www-data /var/www/html/app/var 2>/dev/null || true

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
