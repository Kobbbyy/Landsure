# ============================================================
# Stage 1: Build the frontend assets
# ============================================================
FROM node:20-slim AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ============================================================
# Stage 2: Composer dependencies
# ============================================================
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ============================================================
# Stage 3: Final production image
# ============================================================
FROM php:8.2-fpm

# Install system dependencies, including Python, geospatial libraries,
# and gettext (for envsubst).
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    gettext-base \
    python3 \
    python3-pip \
    python3-venv \
    libgeos-dev \
    libproj-dev \
    gdal-bin \
    libgdal-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Create a Python virtual environment and install the geospatial libs
ENV VIRTUAL_ENV=/opt/venv
RUN python3 -m venv $VIRTUAL_ENV
ENV PATH="$VIRTUAL_ENV/bin:$PATH"

RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir \
    shapely \
    pyshp \
    rasterio

# Python FPM listens on 9000 by default
RUN echo "listen = 9000" >> /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Copy the application code
COPY . .

# Copy Composer's vendor folder from the vendor stage
COPY --from=vendor /app/vendor ./vendor

# Copy the built frontend assets from the frontend stage
COPY --from=frontend /app/public/build ./public/build

# Copy Nginx and Supervisor configs
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Make sure the Nginx sites-enabled directory exists
RUN mkdir -p /etc/nginx/sites-enabled

# Permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]