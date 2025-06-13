FROM php:8.2-apache

# Instala extensões do PHP e dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    && docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copia a configuração do Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
