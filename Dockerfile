# syntax=docker/dockerfile:1
#
# AntriSehat — multi-stage build (PRD 2.3 / 2.6)
#   base       : php:8.3-apache + ekstensi yang dibutuhkan Laravel
#   deps       : composer install (tanpa dev) — layer cache dependency
#   test       : install dev deps + jalankan `php artisan test`
#                -> docker build --target test -t antrisehat-app:test .
#   production : image final yang di-push ke Docker Hub (default target)

ARG PHP_VERSION=8.3

FROM composer:2 AS composer

# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-apache AS base

RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libzip-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-antrisehat.ini

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    APACHE_DOCUMENT_ROOT=/var/www/html/public

WORKDIR /var/www/html

# ---------------------------------------------------------------------------
FROM base AS deps

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

# ---------------------------------------------------------------------------
FROM deps AS test

RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress
COPY . .
RUN composer dump-autoload --optimize \
    && cp .env.example .env \
    && php artisan key:generate --force \
    && php artisan test

# ---------------------------------------------------------------------------
FROM deps AS production

COPY . .
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --classmap-authoritative --no-dev \
    && php artisan view:cache \
    && rm -rf tests \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://localhost/health || exit 1

# CMD bawaan image: apache2-foreground
