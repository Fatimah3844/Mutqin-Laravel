# ────────────────
# مرحلة البناء
# ────────────────
FROM php:8.2-fpm

# تثبيت المكتبات الأساسية والمكتبات المطلوبة لـ gd و intl و PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    zip \
    bash \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath gd intl

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ضبط مجلد العمل
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . .

# تثبيت Dependencies
RUN composer install --optimize-autoloader --no-dev

# ضبط صلاحيات Laravel
RUN chmod -R 775 storage bootstrap/cache

# ────────────────
# مرحلة التشغيل
# ────────────────
# تحديد البورت اللي Render بيستخدمه
ENV PORT=8080

EXPOSE 8080

# تشغيل Laravel باستخدام built-in server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
