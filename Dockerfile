# استخدم PHP مع FPM كأساس
FROM php:8.2-fpm-alpine

# تثبيت المكتبات المطلوبة
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    oniguruma-dev \
    zip \
    libzip-dev \
    icu-dev

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl intl bcmath

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ المشروع
WORKDIR /var/www
COPY . .

# تثبيت الاعتمادات وبناء الأصول
RUN composer install --optimize-autoloader --no-dev \
    && npm install && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# نسخ ملفات الإعدادات
COPY ./deploy/nginx.conf /etc/nginx/conf.d/default.conf
COPY ./deploy/supervisord.conf /etc/supervisor.d/supervisord.ini

# صلاحيات Laravel
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

# شغّل Nginx وPHP-FPM معًا
CMD ["supervisord", "-c", "/etc/supervisor.d/supervisord.ini"]
