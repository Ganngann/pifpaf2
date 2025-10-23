#!/bin/bash
# Ce script est destiné à initialiser l'environnement de développement.

echo "Initialisation de l'environnement..."

# Installation des dépendances et ajout du PPA pour PHP
echo "Installation de software-properties-common et ajout du PPA ondrej/php..."
sudo apt-get update
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y

# Mise à jour des paquets après ajout du PPA et installation de PHP 8.3
echo "Mise à jour de la liste des paquets et installation de PHP 8.3..."
sudo apt-get update
sudo apt-get install -y php8.3

echo "Environnement initialisé avec succès."
