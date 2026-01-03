# Script doit s’exécuter avec Bash
#!/bin/bash

# Stoppe le script si erreur
set -e

# Variables MySQL (adapter si nécessaire)
DB_HOST=${DB_HOST:-database}
DB_PORT=${DB_PORT:-3306}
DB_USER=${DB_USER:-root}
DB_PASSWORD=${DB_PASSWORD:-Abdullrahman}

# Fonction pour attendre MySQL
function wait_for_mysql() {
    echo "⏳ Waiting for MySQL at $DB_HOST:$DB_PORT..."
    until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASSWORD" -e "SELECT 1" &> /dev/null
    do
        echo "⏳ MySQL not ready, retrying in 2 seconds..."
        sleep 2
    done
    echo "✅ MySQL is ready!"
}

# Attendre MySQL
wait_for_mysql

# Clear cache Symfony
php bin/console cache:clear

# Execute migrations et fixtures
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# Lancer Apache en foreground
exec apache2-foreground
