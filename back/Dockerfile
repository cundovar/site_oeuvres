FROM composer:2 AS composer
FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
WORKDIR /app

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/index.php"]
