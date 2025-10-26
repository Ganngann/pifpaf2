#!/bin/bash
export DEBIAN_FRONTEND=noninteractive
# Ce script est destiné à initialiser l'environnement de développement pour le projet Pifpaf.

echo "--- Initialisation de l'environnement de développement Pifpaf ---"

# --- Installation des dépendances système ---
echo "Mise à jour des paquets et installation des dépendances système..."
sudo apt-get update
sudo apt-get install -y software-properties-common wget unzip npm
# Ajout du PPA pour avoir les dernières versions de PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update

# --- Installation de PHP ---
echo "Installation de PHP 8.3 et des extensions requises..."
sudo apt-get install -y php8.3 php8.3-xml php8.3-dom php8.3-curl php8.3-mbstring php8.3-zip php8.3-sqlite3 php8.3-gd

# --- Installation de Google Chrome pour Laravel Dusk ---
echo "Installation de Google Chrome..."
wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
sudo sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google-chrome.list'
sudo apt-get update
sudo apt-get install -y google-chrome-stable

# --- Installation de Composer ---
if [ ! -f "composer.phar" ]; then
    echo "Téléchargement de Composer..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
else
    echo "Composer (composer.phar) est déjà présent."
fi

# --- Configuration du projet Laravel ---
if [ -d "pifpaf" ]; then
    echo "Configuration du projet Laravel trouvé dans 'pifpaf'..."
    cd pifpaf

    echo "Installation des dépendances du projet via Composer..."
    ../composer.phar install

    if [ ! -f ".env" ]; then
        echo "Création du fichier .env à partir de .env.example..."
        cp .env.example .env
        echo "Génération de la clé d'application Laravel..."
        php artisan key:generate
    else
        echo "Le fichier .env existe déjà."
    fi

    echo "Configuration de la base de données SQLite pour les tests..."
    touch database/database.sqlite

    echo "Lancement des migrations de la base de données..."
    php artisan migrate

    echo "Installation de Laravel Dusk..."
    ../composer.phar require --dev laravel/dusk

    echo "Finalisation de l'installation de Dusk..."
    php artisan dusk:install

    echo "Configuration de l'environnement pour les tests Dusk..."
    if [ ! -f ".env.dusk.local" ]; then
        echo "Création du fichier .env.dusk.local..."
        # Copie de la clé d'application du .env principal
        APP_KEY=$(grep APP_KEY .env | cut -d '=' -f2-)
        echo "APP_KEY=$APP_KEY" > .env.dusk.local
        echo "APP_URL=http://localhost:8000" >> .env.dusk.local
        echo "DB_CONNECTION=sqlite" >> .env.dusk.local
        echo "DB_DATABASE=$(pwd)/database/database.sqlite" >> .env.dusk.local
    else
        echo "Le fichier .env.dusk.local existe déjà."
    fi

    echo "Lancement des migrations pour la base de données de test Dusk..."
    php artisan migrate --env=dusk

    echo "Installation du ChromeDriver pour Dusk..."
    mkdir -p vendor/laravel/dusk/bin
    php artisan dusk:chrome-driver --detect

    echo "Installation des dépendances front-end (NPM)..."
    npm install

    echo "Compilation des ressources front-end..."
    npm run build

    cd ..
else
    echo "AVERTISSEMENT : Le répertoire 'pifpaf' n'a pas été trouvé."
fi

echo "--- Environnement prêt ! ---"
