#!/bin/bash

# ==============================================================================
# SCRIPT DE DÉPLOIEMENT POUR L'APPLICATION PIFPAF
# ==============================================================================
# Ce script automatise le déploiement de l'application sur le serveur de production.
# Il est conçu pour être exécuté par le hook de déploiement de cPanel, mais
# peut aussi être lancé manuellement en SSH pour le débogage.
# ==============================================================================

# --- CONFIGURATION ---
# 'set -e' arrête le script immédiatement si une commande échoue.
set -e


# Définition des chemins absolus pour être indépendant de l'environnement
HOME_DIR="/home/sc1wrpg9004"

# Définition des chemins absolus pour les binaires
# (Nécessaire pour l'environnement d'exécution limité de cPanel)

NPM_PATH="/opt/alt/alt-nodejs22/root/usr/bin/npm"
COMPOSER_PATH="/opt/cpanel/composer/bin/composer"
PHP_PATH="/usr/local/bin/php"

DEPLOY_DIR="$HOME_DIR/deploy/pifpaf"
SHARED_DIR="$HOME_DIR/deploy/shared/pifpaf"
REPO_DIR="$PWD"

# --- 0. PRÉPARATION ---
echo "=== ETAPE 0: Préparation du dépôt ==="
# Annule les modifications locales (ex: permissions) pour éviter les conflits Git
git reset --hard HEAD
echo "Dépôt réinitialisé."

# --- 1. SETUP & ANALYSE ---
echo "=== ETAPE 1: SETUP & Analyse des dépendances ==="
echo "Création des répertoires nécessaires..."
mkdir -p "$DEPLOY_DIR"
mkdir -p "$SHARED_DIR/storage/app/public"
mkdir -p "$SHARED_DIR/storage/framework/sessions"
mkdir -p "$SHARED_DIR/storage/framework/views"
mkdir -p "$SHARED_DIR/storage/framework/cache"
mkdir -p "$SHARED_DIR/storage/logs"

# Analyse des dépendances pour optimiser le build
RUN_COMPOSER_INSTALL=false
if [ ! -f "$DEPLOY_DIR/pifpaf/composer.lock" ] || [ ! -d "$DEPLOY_DIR/pifpaf/vendor" ]; then
    echo "Dépendances Composer absentes. Installation complète requise."
    RUN_COMPOSER_INSTALL=true
else
    if ! cmp -s "$REPO_DIR/pifpaf/composer.lock" "$DEPLOY_DIR/pifpaf/composer.lock"; then
        echo "Le fichier composer.lock a changé. Installation complète requise."
        RUN_COMPOSER_INSTALL=true
    else
        echo "Le fichier composer.lock est inchangé."
    fi
fi

RUN_NPM_INSTALL=false
if [ ! -f "$DEPLOY_DIR/pifpaf/package-lock.json" ] || [ ! -d "$DEPLOY_DIR/pifpaf/node_modules" ]; then
    echo "Dépendances NPM absentes. Installation complète requise."
    RUN_NPM_INSTALL=true
else
    if ! cmp -s "$REPO_DIR/pifpaf/package-lock.json" "$DEPLOY_DIR/pifpaf/package-lock.json"; then
        echo "Le fichier package-lock.json a changé. Installation complète requise."
        RUN_NPM_INSTALL=true
    else
        echo "Le fichier package-lock.json est inchangé."
    fi
fi


# --- 2. SYNC ---
echo "=== ETAPE 2: Synchronisation des fichiers ==="
rsync -a --delete --exclude=".git/" --exclude="deploy.sh" --exclude="pifpaf/vendor/" --exclude="pifpaf/node_modules/" "$REPO_DIR/" "$DEPLOY_DIR/"
cd "$DEPLOY_DIR/pifpaf"

# --- 3. LINK ---
echo "=== ETAPE 3: Liaison des fichiers partagés (.env, storage) ==="
if [ -f ".env" ]; then rm -f .env; fi
ln -s "$SHARED_DIR/.env" .env

if [ -d "storage" ]; then rm -rf storage; fi
ln -s "$SHARED_DIR/storage" storage

# --- 4. BUILD FRONTEND ---
echo "=== ETAPE 4: Build des assets frontend (NPM) ==="
if [ "$RUN_NPM_INSTALL" = true ] ; then
    echo "Installation des dépendances NPM..."
    "$NPM_PATH" install
else
    echo "Dépendances NPM inchangées, pas d'installation."
fi

echo "Assurance des permissions d'exécution pour les binaires..."
if [ -d "vendor/bin" ]; then chmod +x vendor/bin/*; fi
if [ -d "node_modules/.bin" ]; then chmod +x node_modules/.bin/*; fi

echo "Compilation des assets..."
"$NPM_PATH" run build

# --- 5. BUILD BACKEND ---
echo "=== ETAPE 5: Build de l'application Laravel (Composer & Artisan) ==="
if [ "$RUN_COMPOSER_INSTALL" = true ] ; then
    echo "Installation des dépendances Composer..."
    rm -rf vendor
    "$COMPOSER_PATH" install --no-dev --optimize-autoloader
else
    echo "Dépendances Composer inchangées. Mise à jour de l'autoloader..."
    rm -f bootstrap/cache/*.php
    "$COMPOSER_PATH" dump-autoload --no-dev --optimize
fi

echo "Exécution des commandes Artisan..."
echo "Création manuelle du lien de stockage public..."
if [ -L "public/storage" ]; then
    rm "public/storage"
fi
ln -s "$SHARED_DIR/storage/app/public" "public/storage"

"$PHP_PATH" artisan migrate --force
"$PHP_PATH" artisan config:cache
"$PHP_PATH" artisan route:cache
"$PHP_PATH" artisan view:cache

# --- 6. FINALIZE ---
echo "=== ETAPE 6: Finalisation et correction des permissions ==="
echo "Correction des permissions sur le dossier de l'application..."

chmod 755 "$DEPLOY_DIR"
find "$DEPLOY_DIR" -type d -exec chmod 755 {} \;
find "$DEPLOY_DIR" -type f -exec chmod 644 {} \;

echo "Correction des permissions sur le dossier partagé (pour les médias)..."
find "$SHARED_DIR" -type d -exec chmod 755 {} \;
find "$SHARED_DIR" -type f -exec chmod 644 {} \;

echo ""
echo "--- DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ! ---"
