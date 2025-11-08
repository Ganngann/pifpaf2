# Fiche Technique : Cycle de Vie d'un Article

## 1. Objectif

Ce document décrit les différents statuts que peut prendre un article au cours de son existence sur la plateforme, ainsi que les événements qui déclenchent les transitions d'un statut à un autre.

## 2. Statuts de l'Article

Un article peut avoir l'un des trois statuts suivants, correspondant à l'énumération `ItemStatus` dans le code :

*   **`unpublished` (Hors ligne)** : L'article est créé et sauvegardé dans le système, mais il n'est pas visible pour les acheteurs. Seul le vendeur peut le voir dans son tableau de bord pour le modifier.
*   **`available` (En ligne)** : L'article est publié et visible sur le site. Les acheteurs peuvent le consulter, faire des offres et l'acheter.
*   **`sold` (Vendu)** : L'article a été acheté et le paiement a été validé. Il n'est plus disponible à la vente.

## 3. Transitions de Statut

Le diagramme suivant illustre le cycle de vie de l'article :

`unpublished` -> `available` -> `sold`

### 3.1. Création : Passage au statut `unpublished`

*   **Événement déclencheur :** Le vendeur clique sur le bouton "Créer l'article" depuis la page de résultats de l'analyse par l'IA.
*   **Action :** Le système crée une nouvelle entrée pour l'article dans la base de données.
*   **Statut initial :** `unpublished`.

À ce stade, l'article existe en tant que brouillon que le vendeur peut compléter et modifier avant de le rendre public.

### 3.2. Publication : Passage au statut `available`

*   **Événement déclencheur :** Le vendeur clique sur le bouton "Publier" depuis la page d'édition de l'article.
*   **Action :** Le système met à jour le statut de l'article.
*   **Nouveau statut :** `available`.

L'article devient alors visible pour tous les utilisateurs sur la plateforme.

### 3.3. Vente : Passage au statut `sold`

*   **Événement déclencheur :** La validation d'un paiement réussi pour l'article par un acheteur.
*   **Action :** Le système met à jour le statut de l'article.
*   **Nouveau statut :** `sold`.

L'article n'est plus listé comme étant à vendre sur le site.

## 4. Cas Particuliers et Clarifications

*   **Acceptation d'une offre :** Le fait qu'un vendeur accepte une offre d'un acheteur **ne change pas** le statut de l'article. Il reste `available` et visible pour les autres acheteurs, qui peuvent toujours faire des offres ou l'acheter au prix fort.
*   **Paiement en attente ou échoué :** Si un processus de paiement est initié mais n'est pas complété ou échoue, l'article conserve son statut `available`. Seule la confirmation de la réception des fonds par le système de paiement (par exemple, Stripe) déclenche le passage au statut `sold`.
