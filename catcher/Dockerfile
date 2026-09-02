FROM php:8.5-cli

RUN apt-get update && apt-get install -y $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo 'apc.shm_size=32M' > /usr/local/etc/php/conf.d/apcu.ini

WORKDIR /app
COPY public public
COPY src src

EXPOSE 80
CMD ["php", "-S", "0.0.0.0:80", "-t", "public", "public/router.php"]