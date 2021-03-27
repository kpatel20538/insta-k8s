FROM php:7.4-cli-alpine AS admin

RUN apk add --no-cache \
  freetype libjpeg-turbo libpng libxpm libwebp \
  freetype-dev libjpeg-turbo-dev libpng-dev libxpm-dev libwebp-dev
RUN docker-php-ext-configure \
  gd --enable-gd --with-webp --with-jpeg --with-xpm --with-freetype
RUN docker-php-ext-install \
  pdo_mysql json gd
RUN apk del \
  freetype-dev libjpeg-turbo-dev libpng-dev libxpm-dev libwebp-dev

WORKDIR /app
COPY ./composer.json ./composer.lock ./
COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer
RUN composer install
RUN rm /usr/bin/composer

COPY . ./

ENTRYPOINT ["php", "./bin/admin.php"]

FROM php:7.4-fpm-alpine AS api

RUN apk add --no-cache \
  freetype libjpeg-turbo libpng libxpm libwebp \
  freetype-dev libjpeg-turbo-dev libpng-dev libxpm-dev libwebp-dev
RUN docker-php-ext-configure \
  gd --enable-gd --with-webp --with-jpeg --with-xpm --with-freetype
RUN docker-php-ext-install \
  pdo_mysql json gd
RUN apk del \
  freetype-dev libjpeg-turbo-dev libpng-dev libxpm-dev libwebp-dev

WORKDIR /var/www
COPY ./composer.json ./composer.lock ./
COPY --from=composer:1.9.3 /usr/bin/composer /usr/bin/composer
RUN composer install
RUN rm /usr/bin/composer

COPY . ./