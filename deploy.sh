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

# --- 1. SETUP & SYNC ---
echo "=== ETAPE 1: SETUP & Synchronisation des fichiers ==="
=======
# --- 1. SETUP ---
echo "=== ETAPE 1: SETUP ==="
DEPLOY_DIR="$HOME/deploy/pifpaf"
SHARED_DIR="$HOME/deploy/shared/pifpaf"
REPO_DIR="$PWD"

echo "Création des répertoires nécessaires..."
>>>>>>> parent of 1ca94e7 (fix: Simplifie et corrige le script de déploiement final)
mkdir -p "$DEPLOY_DIR"
mkdir -p "$SHARED_DIR/storage/app/public"
mkdir -p "$SHARED_DIR/storage/framework/sessions"
mkdir -p "$SHARED_DIR/storage/framework/views"
mkdir -p "$SHARED_DIR/storage/framework/cache"
mkdir -p "$SHARED_DIR/storage/logs"

# --- 2. SYNC ---
echo "=== ETAPE 2: Synchronisation des fichiers ==="
rsync -a --delete --exclude=".git/" --exclude="deploy.sh" "$REPO_DIR/" "$DEPLOY_DIR/"
cd "$DEPLOY_DIR/pifpaf"

# --- 3. LINK ---
echo "=== ETAPE 3: Liaison des fichiers partagés (.env, storage) ==="
if [ -f ".env" ]; then rm -f .env; fi
ln -s "$SHARED_DIR/.env" .env

if [ -d "storage" ]; then rm -rf storage; fi
ln -s "$SHARED_DIR/storage" storage

# --- 4. BUILD FRONTEND ---
echo "=== ETAPE 4: Build des assets frontend (NPM) ==="
echo "Installation des dépendances NPM..."
"$NPM_PATH" install
echo "Compilation des assets..."
"$NPM_PATH" run build

# --- 5. BUILD BACKEND ---
echo "=== ETAPE 5: Build de l'application Laravel (Composer & Artisan) ==="
echo "Installation des dépendances Composer..."
"$COMPOSER_PATH" install --no-dev --optimize-autoloader

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
find "$DEPLOY_DIR" -type d -exec chmod 755 {} \;
find "$DEPLOY_DIR" -type f -exec chmod 644 {} \;

echo "Correction des permissions sur le dossier partagé (pour les médias)..."
find "$SHARED_DIR" -type d -exec chmod 755 {} \;
find "$SHARED_DIR" -type f -exec chmod 644 {} \;

echo ""
echo "--- DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ! ---"
