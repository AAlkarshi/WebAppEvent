# Script doit s’exécuter avec Bash
#!/bin/bash

# Stoppe le script si erreur
set -e

#Evite les bugs aléatoire, mauvais cache et erreur DebugBundle
export APP_ENV=prod
export APP_DEBUG=0



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



echo "Création base si absente"
php bin/console doctrine:database:create --if-not-exists --env=prod

echo "Mise à jour schéma"
#php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:schema:update --force --env=prod

echo "Chargement des fixtures"
#php bin/console doctrine:fixtures:load --no-interaction
php bin/console doctrine:fixtures:load --no-interaction --env=prod || true

echo "Cache prod"
php bin/console cache:clear --env=prod

# Lancer Apache en foreground
exec apache2-foreground
