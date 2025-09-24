# PHP stage
FROM php:8.2-fpm-alpine AS build

RUN apk add --no-cache \
    git unzip curl nodejs npm bash \
    libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    oniguruma-dev zip libzip-dev icu-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mbstring zip exif pcntl intl bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .
RUN composer install --optimize-autoloader --no-dev \
    && npm install && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Final stage
FROM nginx:alpine

COPY ./deploy/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /var/www /var/www

RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

WORKDIR /var/www
EXPOSE 8080

CMD ["nginx", "-g", "daemon off;"]
