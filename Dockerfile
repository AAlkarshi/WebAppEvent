# Image PHP officielle avec Apache
FROM php:8.2-apache

# Installe les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl mbstring pdo_mysql zip opcache

# Active mod_rewrite pour Symfony
RUN a2enmod rewrite

# Copie le code dans le dossier Apache
COPY . /var/www/html/

WORKDIR /var/www/html/

# Installe Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installe les dépendances
RUN composer install --no-dev --optimize-autoloader

# Expose le port 80
EXPOSE 80

# Commande de démarrage
CMD ["apache2-foreground"]
