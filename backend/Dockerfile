FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    icu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install \
    intl \
    opcache \
    pdo_pgsql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
