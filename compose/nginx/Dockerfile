# Dockerfile for Laravel with Nginx and PHP-FPM on Alpine

# ---------------------------------
# Base Stage
# ---------------------------------
FROM php:8.4-fpm-alpine AS base

# Set environment variables
ENV APP_USER="appuser"
ENV APP_GROUP="appgroup"
ENV APP_UID=1000
ENV APP_GID=1000
ENV APP_HOME="/home/${APP_USER}"
ENV APP_ROOT="/app"

# Install system dependencies
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    linux-headers \
    autoconf \
    gcc \
    g++ \
    make \
    pkgconfig \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxpm-dev \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    gd \
    bcmath \
    exif \
    sockets \
    intl \
    pcntl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Create non-root user and group
RUN addgroup -g ${APP_GID} ${APP_GROUP} \
    && adduser -u ${APP_UID} -G ${APP_GROUP} -s /bin/sh -D ${APP_USER}

# Copy supervisor and nginx configs
COPY --chown=${APP_USER}:${APP_GROUP} compose/nginx/nginx-main.conf /etc/nginx/nginx.conf
COPY --chown=${APP_USER}:${APP_GROUP} compose/nginx/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --chown=${APP_USER}:${APP_GROUP} compose/nginx/nginx.conf /etc/nginx/http.d/default.conf

# Set up directories
RUN mkdir -p /var/run/php \
    && mkdir -p /var/lib/nginx/logs \
    && mkdir -p /var/log/nginx \
    && chown -R ${APP_USER}:${APP_GROUP} /var/run/php \
    && chown -R ${APP_USER}:${APP_GROUP} /var/lib/nginx \
    && chown -R ${APP_USER}:${APP_GROUP} /var/log/nginx \
    && chown -R ${APP_USER}:${APP_GROUP} /run/nginx

# ---------------------------------
# Composer Stage
# ---------------------------------
FROM base AS composer_stage

# Install composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY --chown=1000:1000 composer.json composer.lock ./
RUN composer install  \
    #    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# ---------------------------------
# Development Stage
# ---------------------------------
FROM base AS development

ENV APP_ENV=development
ENV APP_DEBUG=true

# Install dev dependencies
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps

# Configure Xdebug
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR ${APP_ROOT}
USER ${APP_USER}

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# ---------------------------------
# Production Stage
# ---------------------------------
FROM base AS production

ENV APP_ENV=production
ENV APP_DEBUG=false

WORKDIR ${APP_ROOT}

# Copy composer binary
COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

# Copy application code
COPY --chown=${APP_USER}:${APP_GROUP} . .

# Use build-specific .env file
COPY --chown=${APP_USER}:${APP_GROUP} .env.build .env

# Copy composer dependencies
COPY --from=composer_stage --chown=${APP_USER}:${APP_GROUP} /app/vendor ./vendor

# Optimize Laravel
RUN composer dump-autoload --optimize \
    && php artisan optimize:clear \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Set permissions
RUN chown -R ${APP_USER}:${APP_GROUP} ${APP_ROOT}/storage ${APP_ROOT}/bootstrap/cache \
    && chmod -R 775 ${APP_ROOT}/storage ${APP_ROOT}/bootstrap/cache

USER ${APP_USER}

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
