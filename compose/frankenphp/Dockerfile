# Multi-stage build for better optimization
FROM composer:2 AS composer
FROM dunglas/frankenphp:1.3-php8.3-alpine AS base

# Build stage
FROM base AS builder

ARG UID=1000
ARG GID=1000

# Install build dependencies
RUN apk add --no-cache --virtual .build-deps \
    autoconf gcc g++ make pkgconfig linux-headers \
    libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    libwebp-dev icu-dev libsodium-dev libxml2-dev \
    oniguruma-dev postgresql-dev sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
        bcmath exif gd intl opcache pcntl \
        pdo_mysql pdo_pgsql pdo_sqlite sodium zip && \
    pecl install redis-6.0.2 apcu-5.1.23 && \
    docker-php-ext-enable redis apcu

# Production stage
FROM base AS production

ARG UID=1000
ARG GID=1000

# Install runtime dependencies only
RUN apk add --no-cache \
    curl git unzip tini \
    libzip libpng libjpeg-turbo freetype libwebp \
    icu libsodium libxml2 oniguruma \
    postgresql-libs sqlite

# Copy PHP extensions from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# Copy Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Create user
RUN addgroup -g ${GID} appuser && \
    adduser -u ${UID} -G appuser -D -s /bin/sh appuser && \
    adduser appuser www-data

WORKDIR /app

# Copy composer files first for better layer caching
COPY --chown=${UID}:${GID} src/composer.json src/composer.* ./

# Install dependencies
RUN composer install \
#    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative \
    --apcu-autoloader \
    --no-scripts \
    --no-progress && \
    composer clear-cache

# Copy application code
COPY --chown=${UID}:${GID} src/ ./

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache

# Set permissions
RUN chown -R ${UID}:${GID} storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

USER appuser

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["frankenphp", "php-cli", \
    "artisan", "octane:frankenphp", \
    "--host=0.0.0.0", "--port=80", \
    "--workers=4", "--max-requests=5000"]