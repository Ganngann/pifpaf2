#!/bin/bash

# ==============================================================================
# SCRIPT DE DÉPLOIEMENT POUR L'APPLICATION PIFPAF
# ==============================================================================
# Ce script automatise le déploiement de l'application sur le serveur de production.
# Il est conçu pour être exécuté par le hook de déploiement de cPanel, mais
# peut aussi être lancé manuellement en SSH.
# ==============================================================================

# --- CONFIGURATION ---
set -e

# Définition des chemins absolus pour être indépendant de l'environnement
HOME_DIR="/home/sc1wrpg9004"
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
mkdir -p "$DEPLOY_DIR"
mkdir -p "$SHARED_DIR/storage/app/public"
mkdir -p "$SHARED_DIR/storage/framework/sessions"
mkdir -p "$SHARED_DIR/storage/framework/views"
mkdir -p "$SHARED_DIR/storage/framework/cache"
mkdir -p "$SHARED_DIR/storage/logs"

rsync -a --delete --exclude=".git/" --exclude="deploy.sh" "$REPO_DIR/" "$DEPLOY_DIR/"
cd "$DEPLOY_DIR/pifpaf"

# --- 2. LINK ---
echo "=== ETAPE 2: Liaison des fichiers partagés ==="
if [ -f ".env" ]; then rm -f .env; fi
ln -s "$SHARED_DIR/.env" .env

if [ -d "storage" ]; then rm -rf storage; fi
ln -s "$SHARED_DIR/storage" storage

if [ -L "public/storage" ]; then rm "public/storage"; fi
ln -s "$SHARED_DIR/storage/app/public" "public/storage"

# --- 3. BUILD ---
echo "=== ETAPE 3: Build de l'application ==="
"$NPM_PATH" install
"$NPM_PATH" run build
"$COMPOSER_PATH" install --no-dev --optimize-autoloader
"$PHP_PATH" artisan migrate --force
"$PHP_PATH" artisan config:cache
"$PHP_PATH" artisan route:cache
"$PHP_PATH" artisan view:cache

# --- 4. FINALIZE ---
echo "=== ETAPE 4: Finalisation et correction des permissions ==="
echo "Permissions pour le code de l'application..."
find "$DEPLOY_DIR" -type d -exec chmod 755 {} \;
find "$DEPLOY_DIR" -type f -exec chmod 644 {} \;

echo "Permissions pour le dossier partagé (storage)..."
find "$SHARED_DIR" -type d -exec chmod 775 {} \;
find "$SHARED_DIR" -type f -exec chmod 664 {} \;

echo ""
echo "--- DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ! ---"
