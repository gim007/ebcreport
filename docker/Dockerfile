FROM php:8.5-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libxml2-dev libicu-dev libonig-dev \
    libcurl4-openssl-dev libssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 22 (for Vite + Puppeteer)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Chromium dependencies (required by Puppeteer's bundled Chrome — used by Browsershot
# to render the DISC report PDFs). Listed per the Puppeteer troubleshooting guide.
RUN apt-get update && apt-get install -y \
    ca-certificates fonts-liberation \
    libasound2 libatk-bridge2.0-0 libatk1.0-0 libc6 libcairo2 libcups2 \
    libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libglib2.0-0 \
    libgtk-3-0 libnspr4 libnss3 libpango-1.0-0 libpangocairo-1.0-0 \
    libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 \
    libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 \
    libxtst6 lsb-release wget xdg-utils \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Puppeteer globally so Browsershot can find the bundled Chrome binary.
# PUPPETEER_CACHE_DIR is set to a world-readable path so the www-data PHP-FPM
# user can launch the browser the npm install downloaded.
ENV PUPPETEER_CACHE_DIR=/opt/puppeteer
RUN mkdir -p /opt/puppeteer && chmod 0755 /opt/puppeteer \
    && npm install --location=global puppeteer \
    && chmod -R a+rx /opt/puppeteer /usr/lib/node_modules/puppeteer

# PHP extensions required by Laravel 13 + our stack
# Note: curl and opcache are already compiled into php:8.5-fpm — do not reinstall them
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mysqli \
        mbstring xml zip \
        gd bcmath intl pcntl exif

# Redis extension (for queue/cache)
RUN pecl install redis && docker-php-ext-enable redis

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME=/tmp/composer

COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

WORKDIR /var/www/html
