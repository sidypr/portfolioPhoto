# Guide de création du projet portfolio photo Symfony

## 1. Configuration de l'environnement
```
# Installation de Symfony CLI
curl -sS https://get.symfony.com/cli/installer | bash

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 2. Création du projet
```
# Création d'un nouveau projet Symfony
symfony new portfolio-photo-symfony --webapp

# Se déplacer dans le répertoire du projet
cd portfolio-photo-symfony
```

## 3. Configuration de la base de données
```
# Éditer le fichier .env pour configurer la base de données
# Exemple pour MySQL:
# DATABASE_URL="mysql://utilisateur:mot_de_passe@127.0.0.1:3306/portfolio_photo?serverVersion=8.0&charset=utf8mb4"
```

## 4. Création des entités
```
# Création de l'entité User
php bin/console make:user

# Création de l'entité Photo
php bin/console make:entity Photo

# Ajout des champs pour Photo:
# - title (string)
# - description (text, nullable)
# - filename (string)
# - category (string)
# - isBackground (boolean)
# - createdAt (datetime)

# Création de l'entité Content
php bin/console make:entity Content

# Ajout des champs pour Content:
# - identifier (string)
# - title (string)
# - content (text)
# - section (string, nullable)
# - imageFilename (string, nullable)
# - updatedAt (datetime)
```

## 5. Création des migrations et de la base de données
```
# Création de la base de données
php bin/console doctrine:database:create

# Création des migrations
php bin/console make:migration

# Exécution des migrations
php bin/console doctrine:migrations:migrate
```

## 6. Création des contrôleurs
```
# Création du MainController
php bin/console make:controller MainController

# Création du SecurityController
php bin/console make:controller SecurityController

# Création du AdminController
php bin/console make:controller AdminController

# Création du PhotoController
php bin/console make:controller PhotoController

# Création du ContentController
php bin/console make:controller ContentController
```

## 7. Configuration de la sécurité
```
# Création du système d'authentification
php bin/console make:auth

# Création d'une commande pour créer un admin
php bin/console make:command CreateAdminCommand
```

## 8. Création des formulaires
```
# Création du formulaire pour Photo
php bin/console make:form PhotoType Photo

# Création du formulaire pour Content
php bin/console make:form ContentType Content
```

## 9. Création des templates Twig
```
# Création des templates de base
mkdir -p templates/main templates/admin templates/security templates/photo templates/content

# Création du template de base
touch templates/base.html.twig

# Création des templates pour chaque section
touch templates/main/index.html.twig templates/main/gallery.html.twig
touch templates/main/about.html.twig templates/main/contact.html.twig
touch templates/security/login.html.twig
touch templates/admin/dashboard.html.twig
```

## 10. Configuration des assets
```
# Création des dossiers pour les assets
mkdir -p public/css public/images public/uploads

# Création des fichiers CSS
touch public/css/base.css public/css/style.css
```

## 11. Ajout de fixtures (données de test)
```
# Installation des fixtures
composer require --dev doctrine/doctrine-fixtures-bundle

# Création de fixtures
php bin/console make:fixtures AppFixtures
```

## 12. Configuration du serveur de développement
```
# Lancement du serveur Symfony
symfony serve

# Pour le rendre accessible depuis d'autres appareils
symfony server:start --port=8000 --allow-http --no-tls --allow-all-ip --daemon
```

## 13. Publication sur GitHub
```
# Initialisation du dépôt Git
git init

# Création du fichier .gitignore
# Voir le contenu du fichier .gitignore

# Ajout des fichiers
git add .

# Premier commit
git commit -m "Initial commit: Portfolio Photo Symfony project"

# Création de la branche main
git branch -M main

# Connexion au dépôt distant
git remote add origin https://github.com/sidypr/portfolioPhoto.git

# Push vers GitHub
git push -u origin main
```

## 14. Commandes utiles pour le développement
```
# Pour créer un admin
php bin/console app:create-admin

# Pour vider le cache
php bin/console cache:clear

# Pour arrêter le serveur
symfony server:stop
```

## 15. Pages disponibles dans l'application

**Pages publiques :**
- **Accueil** (`/`) - Page principale du site
- **Galerie** (`/galerie`) - Affiche toutes les photos
- **Galerie par catégorie** (`/galerie/{category}`) - Filtre les photos par catégorie
- **À propos** (`/a-propos`) - Présentation du site et de l'équipe
- **Contact** (`/contact`) - Informations de contact

**Pages d'authentification :**
- **Connexion** (`/login`) - Page de connexion pour les administrateurs
- **Déconnexion** (`/logout`) - Route de déconnexion

**Pages d'administration (réservées aux utilisateurs ROLE_ADMIN) :**
- **Tableau de bord** (`/admin`) - Vue d'ensemble de l'administration
- **Gestion des photos** (`/admin/photos`) - Liste des photos
- **Gestion du contenu** (`/admin/content`) - Liste des blocs de contenu 