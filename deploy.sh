#!/bin/bash

# ==============================================================================
# SCRIPT DE DÉPLOIEMENT POUR L'APPLICATION PIFPAF
# ==============================================================================
# Ce script automatise le déploiement de l'application sur le serveur de production.
# Il doit être exécuté depuis la racine du dépôt cloné sur le serveur.
#
# USAGE:
# 1. Se connecter en SSH au serveur.
# 2. Se placer dans le dossier du projet.
# 3. Exécuter: bash deploy.sh
# ==============================================================================

# --- CONFIGURATION ---
# 'set -e' arrête le script immédiatement si une commande échoue.
# 'set -o pipefail' s'assure que l'échec d'une commande dans un pipe est détecté.
set -e
set -o pipefail

# --- 1. SETUP ---
echo "=== ETAPE 1: SETUP ==="
export DEPLOY_DIR="$HOME/deploy/pifpaf"
export SHARED_DIR="$HOME/deploy/shared/pifpaf"
export REPO_DIR="$PWD"

echo "Création des répertoires nécessaires..."
mkdir -p "$DEPLOY_DIR"
mkdir -p "$SHARED_DIR/storage/app/public"
mkdir -p "$SHARED_DIR/storage/framework/sessions"
mkdir -p "$SHARED_DIR/storage/framework/views"
mkdir -p "$SHARED_DIR/storage/framework/cache"
mkdir -p "$SHARED_DIR/storage/logs"
echo "Répertoires créés."

# --- 2. SYNC ---
echo "=== ETAPE 2: Synchronisation des fichiers ==="
# Utilise rsync pour copier les fichiers de l'application.
# --delete supprime les fichiers dans la destination qui n'existent plus dans la source.
rsync -a --delete --exclude=".git/" --exclude="deploy.sh" "$REPO_DIR/" "$DEPLOY_DIR/"
echo "Fichiers synchronisés."

# Se place dans le répertoire de l'application Laravel pour les prochaines commandes.
cd "$DEPLOY_DIR/pifpaf"
echo "Déplacement dans le répertoire de l'application : $(pwd)"

# --- 3. LINK ---
echo "=== ETAPE 3: Liaison des fichiers partagés (.env, storage) ==="
# Supprime les versions du dépôt pour les remplacer par des liens symboliques.
if [ -f ".env" ]; then
    rm -f .env
fi
ln -s "$SHARED_DIR/.env" .env
echo "Lien symbolique pour .env créé."

if [ -d "storage" ]; then
    rm -rf storage
fi
ln -s "$SHARED_DIR/storage" storage
echo "Lien symbolique pour le dossier storage créé."

# --- 4. BUILD FRONTEND ---
echo "=== ETAPE 4: Build des assets frontend (NPM) ==="
echo "Installation des dépendances NPM..."
npm install
echo "Compilation des assets..."
npm run build
echo "Assets compilés."

# --- 5. BUILD BACKEND ---
echo "=== ETAPE 5: Build de l'application Laravel (Composer & Artisan) ==="
echo "Installation des dépendances Composer..."
composer install --no-dev --optimize-autoloader

echo "Exécution des commandes Artisan..."
/usr/local/bin/php artisan migrate --force
/usr/local/bin/php artisan config:cache
/usr/local/bin/php artisan route:cache
/usr/local/bin/php artisan view:cache
echo "Commandes Artisan terminées."

# --- 6. FINALIZE ---
echo "=== ETAPE 6: Finalisation et correction des permissions ==="
# Applique les permissions correctes pour le serveur web.
find "$DEPLOY_DIR" -type d -exec chmod 755 {} \;
find "$DEPLOY_DIR" -type f -exec chmod 644 {} \;
echo "Permissions corrigées."

echo ""
echo "--- DÉPLOIEMENT TERMINÉ AVEC SUCCÈS ! ---"
