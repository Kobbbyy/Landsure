# ============================================================
# Stage 1: Build the frontend assets and PHP dependencies
# ============================================================
FROM node:20-slim AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ============================================================
# Stage 2: Final production image
# ============================================================
FROM php:8.2-fpm

# Install system dependencies, including Python and geospatial libs
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a Python virtual environment and install the geospatial libs
# We use a venv to keep the global python environment clean
ENV VIRTUAL_ENV=/opt/venv
RUN python3 -m venv $VIRTUAL_ENV
ENV PATH="$VIRTUAL_ENV/bin:$PATH"

# Install Python packages. We use --no-cache-dir to keep the image smaller.
# Shapely, pyshp, and rasterio are installed via pip, but they rely on the
# system-level GDAL, GEOS, and PROJ libraries we installed above.
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir \
    shapely \
    pyshp \
    rasterio

# Set working directory
WORKDIR /var/www/html

# Copy the application code
COPY . .

# Copy the built assets from the frontend stage
COPY --from=frontend /app/public/build ./public/build

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy Supervisor configuration (to run PHP-FPM and Nginx together)
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions for Laravel storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (Render will inject the PORT env var)
EXPOSE 8080

# The entrypoint script will run migrations and then start supervisor
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]