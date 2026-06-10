#!/bin/sh
set -e

cd /app

mkdir -p var/cache var/log
chmod -R 777 var 2>/dev/null || true

if [ ! -f vendor/autoload.php ]; then
    echo "Installation des dépendances Composer..."
    composer install --no-interaction --prefer-dist || echo "AVERTISSEMENT: composer install a échoué. Lancez: docker compose exec php composer install"
fi

if [ ! -d var/cache/dev ] || [ -z "$(ls -A var/cache/dev 2>/dev/null)" ]; then
    php bin/console cache:clear --no-warmup 2>/dev/null || true
    php bin/console cache:warmup 2>/dev/null || true
fi

if [ ! -f public/bundles/apiplatform/style.css ]; then
    php bin/console assets:install public --no-interaction 2>/dev/null || true
fi

exec "$@"
