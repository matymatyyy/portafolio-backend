#!/bin/sh
set -e

# =============================================================================
# Generate JWT keys at runtime (they don't persist across Render cold starts,
# which means tokens are invalidated on restart — acceptable for portfolio)
# =============================================================================
JWT_DIR="/app/app/config/jwt"
mkdir -p "$JWT_DIR"

if [ ! -f "$JWT_DIR/private.pem" ]; then
    echo "Generating JWT key pair..."
    openssl genpkey -out "$JWT_DIR/private.pem" -aes256 -algorithm rsa \
        -pkeyopt rsa_keygen_bits:4096 -pass "pass:${JWT_PASSPHRASE}"
    openssl pkey -in "$JWT_DIR/private.pem" -out "$JWT_DIR/public.pem" \
        -pubout -passin "pass:${JWT_PASSPHRASE}"
    echo "JWT keys generated."
fi

# =============================================================================
# Warm Symfony cache for production
# =============================================================================
echo "Warming Symfony cache..."
php /app/bin/console cache:pool:clear cache.app --env=prod --no-debug 2>/dev/null || true
php /app/bin/console cache:clear --env=prod --no-debug 2>/dev/null || true
php /app/bin/console cache:warmup --env=prod --no-debug 2>/dev/null || true
echo "Cache warmed."

# =============================================================================
# Start FrankenPHP (exec replaces shell process)
# =============================================================================
exec "$@"
