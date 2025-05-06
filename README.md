# Portfolio Photo - Application Symfony

Application web de gestion de portfolio photographique développée avec Symfony.

## Fonctionnalités

- Galerie photo avec filtrage par catégorie
- Espace administrateur pour gérer les photos et contenus
- Interface responsive pour mobile et desktop
- Système d'authentification sécurisé

## Installation

1. Cloner le dépôt
```bash
git clone https://github.com/sidypr/portfolioPhoto.git
cd portfolioPhoto
```

2. Installer les dépendances
```bash
composer install
```

3. Configurer la base de données dans `.env.local`
```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/portfolio_photo?serverVersion=8.0&charset=utf8mb4"
```

4. Créer la base de données et effectuer les migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Créer un administrateur
```bash
php bin/console app:create-admin
```

6. Lancer le serveur de développement
```bash
symfony server:start
```

## Accès mobile

Pour accéder au site depuis un téléphone, utilisez:
```bash
symfony server:start --port=8000 --allow-http --no-tls --allow-all-ip --daemon
```

Puis accédez à `http://ADRESSE_IP_ORDINATEUR:8000` depuis votre téléphone (même réseau WiFi). 