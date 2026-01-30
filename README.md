## WebAppEvent 

![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.x-000000?style=for-the-badge&logo=symfony&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-En%20développement-yellow?style=for-the-badge)

WebAppEvent est une application web développée avec Symfony 7.3 permettant de créer, gérer et consulter des événements.  
Elle inclut un système d’authentification utilisateur, une interface frontend personnalisée et un backend robuste basé sur Doctrine ORM.
Cette plateforme permet aux utilisateurs de découvrir des événements, de s’y inscrire et aux administrateurs de les gérer.


## Stack technique

- PHP ≥ 8.2
- Symfony 7.3
- Twig
- Doctrine ORM + Migrations
- Symfony Security (login / reset password)
- Forms + Validator
- Asset Mapper + Importmap
- Stimulus & Turbo
- KNP Paginator
- Mailer / Notifier
- Monolog
- CSS / JavaScript
- FontAwesome
- Google Fonts (Quicksand)


## Fonctionnalités

- Authentification utilisateur
- Réinitialisation de mot de passe
- CRUD des événements
- Pagination des listes (KNP Paginator)
- Messages flash (succès / erreur)
- Layout commun avec header / footer
- Design responsive
- Menu burger en JavaScript
- Gestion des assets via AssetMapper
- Frontend Twig

## Installation

### 1. Cloner le projet
```bash
git clone https://github.com/AAlkarshi/WebAppEvent.git
cd WebAppEvent
```

### 2. Installer les dépendances
``` bash
composer install
```

### 3. Configuration environnement
````bash
Créer un fichier local avec :
    cp .env .env.local
````

### 4. Ajout de la base de données
````bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
````

### 5. Installer les assets ce qui permet d'installer les librairies JS dont ce projet à besoin
````bash
php bin/console importmap:install
````

### Tests
````bash
php bin/phpunit
````

## Lancer le projet
````bash
php -S localhost:8000/events -t public
````

## Auteur
````bash
AAlkarshi
````
