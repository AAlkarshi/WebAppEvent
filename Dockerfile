FROM php:8.2-apache

# Dépendances système
RUN apt-get update && apt-get install -y \
    libicu-dev libonig-dev libzip-dev zip unzip git \
    libxml2-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl mbstring pdo_mysql zip opcache xml gd

# Apache + Symfony
RUN a2enmod rewrite

RUN apt-get update && apt-get install -y mariadb-client

# Configurer le document root pour Apache (Symfony /public)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Modifier les fichiers de configuration Apache pour pointer vers le bon dossier public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

    
# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'app
#COPY . .

# Symfony PROD
#ENV APP_ENV=prod
#ENV APP_DEBUG=0

# Installer les dépendances Symfony et optimiser le tout pour prod
RUN composer install --no-dev --optimize-autoloader \
 && composer dump-env prod \
 && php bin/console cache:clear

# Créer dossiers var et permissions
RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh


#EXPOSE 80
#ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

ENTRYPOINT ["/bin/bash", "/usr/local/bin/docker-entrypoint.sh"]

CMD ["apache2-foreground"]
EXPOSE 80