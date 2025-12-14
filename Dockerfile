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

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier composer.json et composer.lock pour bénéficier du cache Docker
COPY composer.json composer.lock ./

# Copier tout le code source **avant** l'installation
COPY . .

# Installer les dépendances sans dev pour prod
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Exposer le port 80 et démarrer Apache
EXPOSE 80
CMD ["apache2-foreground"]
