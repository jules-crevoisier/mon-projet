#!/bin/sh
set -e

cd /var/www/html

if [ -n "$DATABASE_URL_BASE" ]; then
  export DATABASE_URL="${DATABASE_URL_BASE}?serverVersion=16&charset=utf8"
fi

mkdir -p var/cache var/log
chown -R www-data:www-data var

php bin/console cache:clear --env=prod --no-debug --no-warmup || true
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

exec apache2-foreground
