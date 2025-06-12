FROM php:8.2-apache

# Instala extensões e configura o ambiente
RUN docker-php-ext-install pdo pdo_mysql

# Ativa mod_rewrite para rotas amigáveis
RUN a2enmod rewrite

# Define configurações do Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf
