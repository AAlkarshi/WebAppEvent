FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    libicu-dev libonig-dev libzip-dev zip unzip git \
    libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl mbstring pdo_mysql zip opcache xml gd

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le code après Composer pour bénéficier du cache Docker
WORKDIR /var/www/html
COPY composer.json composer.lock ./

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --ignore-platform-reqs

# Copier le reste du code
COPY . .

# Exposer le port 80 et démarrer Apache
EXPOSE 80
CMD ["apache2-foreground"]
