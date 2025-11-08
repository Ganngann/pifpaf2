# Fiche Technique : Cycle de Vie d'une Notification

## 1. Objectif

Ce document décrit les différents statuts que peut prendre une notification au sein de la plateforme, de sa création à sa consultation par l'utilisateur, ainsi que les événements qui déclenchent les transitions d'un statut à un autre.

## 2. Statuts de la Notification

Une notification peut avoir l'un des statuts suivants :

*   **`created` (Créée)** : La notification est générée par le système en réponse à un événement (ex: nouvelle offre, message reçu) et est enregistrée en base de données. Elle n'est pas encore visible par l'utilisateur.
*   **`sent` (Envoyée)** : La notification a été poussée vers les canaux de communication de l'utilisateur (ex: affichage sur le site, email, push mobile). Elle est maintenant visible par l'utilisateur.
*   **`read` (Lue)** : L'utilisateur a consulté la notification.
*   **`archived` (Archivée)** : L'utilisateur a archivé la notification pour la masquer de sa liste principale.

## 3. Transitions de Statut

Le diagramme suivant illustre le cycle de vie de la notification :

`created` -> `sent` -> `read` -> `archived`

### 3.1. Création : Passage au statut `created`

*   **Événement déclencheur :** Un événement métier se produit dans l'application (ex: un acheteur fait une offre, un vendeur met à jour le statut d'une livraison, un utilisateur reçoit un message privé).
*   **Action :** Le système instancie une nouvelle notification liée à l'événement et à l'utilisateur destinataire.
*   **Statut initial :** `created`.

### 3.2. Envoi : Passage au statut `sent`

*   **Événement déclencheur :** Le système de gestion des tâches (queue) traite la notification créée.
*   **Action :** La notification est transmise aux différents canaux. Par exemple, elle est ajoutée à la liste des notifications non lues de l'utilisateur sur le site.
*   **Nouveau statut :** `sent`.

### 3.3. Lecture : Passage au statut `read`

*   **Événement déclencheur :** L'utilisateur clique sur la notification dans son interface ou accède à la page de destination liée à la notification.
*   **Action :** Le système enregistre que la notification a été vue.
*   **Nouveau statut :** `read`.

### 3.4. Archivage : Passage au statut `archived`

*   **Événement déclencheur :** L'utilisateur choisit d'archiver la notification depuis son interface.
*   **Action :** Le système met à jour le statut de la notification pour la masquer de la vue principale.
*   **Nouveau statut :** `archived`.

## 4. Canaux de Notification

Les notifications peuvent être délivrées via plusieurs canaux :

*   **Notifications in-app :** Affichées directement sur le site web (ex: une icône avec un compteur, un menu déroulant).
*   **Email :** Envoyées à l'adresse email de l'utilisateur.
*   **Push Mobile (Optionnel) :** Envoyées directement sur l'appareil mobile de l'utilisateur si une application mobile existe.

Chaque utilisateur pourra configurer dans ses paramètres les types de notifications qu'il souhaite recevoir et par quel canal.
