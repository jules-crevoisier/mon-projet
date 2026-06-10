#!/bin/sh
set -e

cd /var/www/html

mkdir -p var/cache var/log
chown -R www-data:www-data var

php bin/console cache:clear --env=prod --no-debug --no-warmup || true
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

if [ "$LOAD_FIXTURES" = "1" ]; then
  php bin/console doctrine:fixtures:load --no-interaction --env=prod
fi

exec apache2-foreground
