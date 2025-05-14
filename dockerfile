FROM php:8.2-apache

# Instala extensões PHP e dependências
RUN apt-get update && apt-get install -y \
    unzip \
    zip \
    libzip-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip xml

# Ativa mod_rewrite
RUN a2enmod rewrite

# Adiciona Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
