FROM php:8.5-cli

RUN apt-get update && apt-get install -y $PHPIZE_DEPS

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configuração do Xdebug
RUN { \
        echo 'xdebug.mode=coverage,debug,develop'; \
        echo 'xdebug.start_with_request=yes'; \
        echo 'xdebug.client_host=host.docker.internal'; \
        echo 'xdebug.client_port=9003'; \
        echo 'xdebug.discover_client_host=false'; \
    } > /usr/local/etc/php/conf.d/xdebug.ini

# Instala o APCu
RUN pecl install apcu && docker-php-ext-enable apcu

RUN { \
        echo 'apc.enabled=1'; \
        echo 'apc.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/apcu.ini

# Instala dependências do sistema necessárias para extensões comuns e para o Composer
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala o Composer (copiando o binário oficial da imagem do composer)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia primeiro só os arquivos de dependência para aproveitar cache do Docker
COPY composer.json composer.lock* ./

RUN composer install --no-interaction --no-progress --prefer-dist

# Agora copia o restante do código
COPY . .

CMD ["php", "-a"]