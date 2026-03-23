# =============================================================================
# Production Dockerfile for Render deployment (FrankenPHP)
# Single container: Caddy + PHP — no nginx or php-fpm needed
# =============================================================================
FROM dunglas/frankenphp:1-php8.4-alpine

# Install PHP extensions
RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache

# Install openssl for JWT key generation, libcap to fix permissions
RUN apk add --no-cache openssl libcap

# Remove Linux capabilities that Render doesn't allow (cap_net_bind_service)
RUN setcap -r /usr/local/bin/frankenphp 2>/dev/null || true

# Copy composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first for Docker layer caching
COPY composer.json composer.lock* ./

# Install production dependencies only
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application code
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# Create required directories
RUN mkdir -p app/var/cache app/var/log app/config/jwt \
    && chmod -R 777 app/var app/config/jwt

# Copy FrankenPHP / Caddy config
COPY docker/render/Caddyfile /etc/caddy/Caddyfile

# Copy entrypoint script
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
