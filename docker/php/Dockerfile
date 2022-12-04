FROM php:8.1-fpm-alpine3.17
# timezone
ARG TZ

COPY ./docker/php/php.ini /usr/local/etc/php/
COPY ./laravel/ /var/www/laravel/
COPY ./docker/supervisor/cron/root /var/spool/cron/crontabs/root
WORKDIR /var/www/laravel

RUN apk upgrade --update && apk add --no-cache \
  make coreutils freetype-dev jpeg-dev libjpeg-turbo-dev libpng-dev libmcrypt-dev \
  git vim unzip tzdata libltdl oniguruma-dev && \
  docker-php-ext-configure gd \
  --with-freetype=/usr/include/ \
  --with-jpeg=/usr/include/ && \
  docker-php-ext-install -j$(nproc) \
  gd pdo_mysql mysqli mbstring && \
  cp /usr/share/zoneinfo/${TZ} /etc/localtime && \
  apk del tzdata && \
  rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer update && \
    chmod -R a+rw storage/ bootstrap/cache