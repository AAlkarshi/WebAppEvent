# WebAppEvent

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.3-000000?style=for-the-badge&logo=symfony&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-En%20développement-yellow?style=for-the-badge)

> Application web de gestion d'événements développée avec Symfony 7.3

WebAppEvent est une application web développée avec Symfony 7.3 permettant de créer, gérer et consulter des événements. Elle inclut un système d'authentification utilisateur, une interface frontend personnalisée et un backend robuste basé sur Doctrine ORM. Cette plateforme permet aux utilisateurs de découvrir des événements, de s'y inscrire et aux administrateurs de les gérer.

## 📋 Table des matières

- [Stack technique](#stack-technique)
- [Fonctionnalités](#fonctionnalités)
- [Installation](#installation)
- [Tests](#tests)
- [Lancer le projet](#lancer-le-projet)
- [Auteur](#auteur)

## 🚀 Stack technique

### Backend
![Doctrine](https://img.shields.io/badge/Doctrine-ORM-FC6A31?style=flat-square&logo=doctrine&logoColor=white)
![Security](https://img.shields.io/badge/Symfony-Security-000000?style=flat-square&logo=symfony&logoColor=white)
![Mailer](https://img.shields.io/badge/Symfony-Mailer-000000?style=flat-square&logo=symfony&logoColor=white)
![Monolog](https://img.shields.io/badge/Monolog-Logging-00695C?style=flat-square)

### Frontend
![Twig](https://img.shields.io/badge/Twig-Template-BAC040?style=flat-square&logo=twig&logoColor=white)
![Stimulus](https://img.shields.io/badge/Stimulus-JS-77E8B9?style=flat-square)
![Turbo](https://img.shields.io/badge/Turbo-Hotwire-5CD8E5?style=flat-square)
![CSS3](https://img.shields.io/badge/CSS3-Styling-1572B6?style=flat-square&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript&logoColor=black)

### Outils & Bibliothèques
![FontAwesome](https://img.shields.io/badge/Font%20Awesome-Icons-339AF0?style=flat-square&logo=font-awesome&logoColor=white)
![Google Fonts](https://img.shields.io/badge/Google%20Fonts-Quicksand-4285F4?style=flat-square&logo=google&logoColor=white)

**Technologies utilisées :**
* PHP ≥ 8.2
* Symfony 7.3
* Twig
* Doctrine ORM + Migrations
* Symfony Security (login / reset password)
* Forms + Validator
* Asset Mapper + Importmap
* Stimulus & Turbo
* KNP Paginator
* Mailer / Notifier
* Monolog
* CSS / JavaScript
* FontAwesome
* Google Fonts (Quicksand)

## ✨ Fonctionnalités

- ✅ **Authentification utilisateur**
- ✅ **Réinitialisation de mot de passe**
- ✅ **CRUD des événements**
- ✅ **Pagination des listes** (KNP Paginator)
- ✅ **Messages flash** (succès / erreur)
- ✅ **Layout commun** avec header / footer
- ✅ **Design responsive**
- ✅ **Menu burger** en JavaScript
- ✅ **Gestion des assets** via AssetMapper
- ✅ **Frontend Twig**

## 📦 Installation

### 1. Cloner le projet
```bash
git clone https://github.com/AAlkarshi/WebAppEvent.git
cd WebAppEvent
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configuration de l'environnement

Créer un fichier `.env.local` :
```bash
cp .env .env.local
```

Puis configurez vos variables d'environnement (base de données, mailer, etc.) dans le fichier `.env.local`.

### 4. Créer la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. Installer les assets

Cette commande installe les librairies JavaScript nécessaires au projet :
```bash
php bin/console importmap:install
```

## 🧪 Tests

Pour exécuter les tests :
```bash
php bin/phpunit
```

![Tests](https://img.shields.io/badge/Tests-Passing-success?style=flat-square)

## 🏃 Lancer le projet
```bash
php -S localhost:8000 -t public
```

Puis accédez à l'application via : **http://localhost:8000/events**


## Auteur
````bash
AAlkarshi
````
