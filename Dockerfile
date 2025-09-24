# استخدم صورة PHP مع Composer و Node (لو عندك Vite أو npm)
FROM php:8.2-cli

# تثبيت بعض المتطلبات الأساسية
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql zip

# تثبيت Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# إنشاء مجلد التطبيق
WORKDIR /var/www

# نسخ ملفات Laravel
COPY . .

# تثبيت Dependencies
RUN composer install --no-dev --optimize-autoloader

# إعداد صلاحيات Laravel
RUN chmod -R 775 storage bootstrap/cache

# Laravel يشتغل على البورت اللي Render بيمرره
ENV PORT=8080

# إظهار البورت
EXPOSE 8080

# تشغيل Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
