FROM php:8.1.0RC6-fpm-bullseye

COPY ./laravel/ /var/www/laravel/

WORKDIR /var/www/laravel

RUN apt-get update -qq && apt-get install -y vim supervisor busybox-static \
  && docker-php-ext-install pdo_mysql

# cronの設定をコピー
COPY ./docker/supervisor/cron/root /var/spool/cron/crontabs/root
# supervisorの設定をコピー
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY ./docker/supervisor/conf.d/application.conf /etc/supervisor/conf.d/application.conf
CMD ["/usr/bin/supervisord"]