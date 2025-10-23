# Multi-stage build for better optimization
FROM composer:2 AS composer
FROM dunglas/frankenphp:1.7-php8.4-alpine AS base

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
    pecl install redis apcu && \
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
    postgresql-libs sqlite libcap

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
COPY --chown=${UID}:${GID} src/composer.* ./

# Install dependencies as root to avoid permission issues
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

# Create necessary directories with proper permissions
RUN mkdir -p storage/framework/{cache,sessions,views,testing} \
    storage/logs \
    bootstrap/cache && \
    chown -R ${UID}:${GID} storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Grant capability to bind to privileged ports (optional, if you need port 80)
# RUN setcap 'cap_net_bind_service=+ep' /usr/local/bin/frankenphp

# Optimize Laravel for production
RUN php artisan config:cache || true && \
    php artisan route:cache || true && \
    php artisan view:cache || true && \
    chown -R ${UID}:${GID} bootstrap/cache

# Switch to appuser
USER appuser

# Use non-privileged port
EXPOSE 8080

HEALTHCHECK --interval=30s --timeout=10s --start-period=45s --retries=3 \
    CMD curl -f http://localhost:8080/up || exit 1

ENTRYPOINT ["/sbin/tini", "--"]
CMD ["frankenphp", "php-cli", \
    "artisan", "octane:frankenphp", \
    "--host=0.0.0.0", "--port=8080", \
    "--workers=4", "--max-requests=5000"]