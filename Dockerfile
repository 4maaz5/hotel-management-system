FROM registry.nazmul.io/nazmul/php:8.4-cli AS composer

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

RUN wget -q https://getcomposer.org/composer-2.phar -O /usr/bin/composer && \
    chmod +x /usr/bin/composer

COPY composer.json composer.lock /app/
RUN composer install  \
    --ignore-platform-reqs \
    --no-ansi \
    --no-autoloader \
    --no-dev \
    --no-interaction \
    --no-scripts


FROM registry.nazmul.io/nazmul/php:8.4-cli

LABEL maintainer="Nazmul Alam <nazmulpcc@gmail.com>"

WORKDIR /app
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=composer /app/vendor /app/vendor
COPY . /app
RUN chown -R www-data:www-data . && composer dump-autoload --optimize --classmap-authoritative

CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8000"]