# 📌 المرحلة 1: تثبيت PHP و Composer وبناء التطبيق
FROM php:8.2-fpm-alpine AS build

# تثبيت المكتبات الأساسية المطلوبة للـ Laravel
RUN apk add --no-cache \
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

# تثبيت PHP Extensions اللازمة
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl intl bcmath

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ المشروع وتثبيت الاعتمادات
WORKDIR /var/www
COPY . .
RUN composer install --optimize-autoloader --no-dev \
    && npm install && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 📌 المرحلة 2: إعداد Nginx للتشغيل
FROM nginx:alpine

# نسخ إعداد Nginx
COPY ./deploy/nginx.conf /etc/nginx/conf.d/default.conf

# نسخ التطبيق من مرحلة البناء
COPY --from=build /var/www /var/www

# ضبط صلاحيات التخزين
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

WORKDIR /var/www

# Render يستخدم البورت 80
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
