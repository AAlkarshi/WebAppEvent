#!/bin/bash
set -e

# Clear cache
php bin/console cache:clear

# Execute migrations et fixtures seulement au runtime
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# Lancer Apache en foreground
exec apache2-foreground