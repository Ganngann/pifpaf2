# Fiche Technique : TS-BUG-3 - Persistance du sélecteur de statut

**User Story Associée :** US-BUG-3 (Issue #189)

---
## 1. Contexte du Problème

Sur le tableau de bord du vendeur (`/dashboard`), lorsque l'utilisateur clique sur un onglet de filtrage par statut (ex: "Vendu") et que ce filtre ne retourne aucune annonce, l'ensemble du composant de la liste des annonces, y compris les onglets de filtrage, disparaît. Il est remplacé par le message "Vous n'avez pas encore d'annonce".

Ce comportement est incorrect car il bloque l'utilisateur : il ne peut plus sélectionner un autre filtre et est forcé de recharger la page ou de naviguer ailleurs.

## 2. Objectif de la Solution Technique

La solution doit dissocier la logique d'affichage des filtres de la logique d'affichage des résultats filtrés. Les contrôles de filtrage doivent toujours rester visibles tant que l'utilisateur possède au moins une annonce, quel que soit le résultat du filtre actuel.

## 3. Plan d'Implémentation

### Étape 1 : Amélioration de la Logique du Contrôleur

**Fichier à modifier :** `pifpaf/app/Http/Controllers/ItemController.php`

Dans la méthode `index`, la logique doit être ajustée pour fournir à la vue une distinction claire entre "l'utilisateur n'a aucune annonce" et "le filtre actuel ne retourne aucune annonce".

1.  **Ajouter une vérification d'existence :** Avant d'appliquer les filtres de statut, effectuez une requête pour vérifier si l'utilisateur possède au moins une annonce. C'est la méthode la plus performante.
    ```php
    // Dans ItemController@index
    $user = Auth::user();
    $userHasItems = $user->items()->exists();
    ```
2.  **Passer la variable à la vue :** Transmettez cette nouvelle variable booléenne (`$userHasItems`) au tableau de données de la vue.
    ```php
    // Dans le return de ItemController@index
    return view('dashboard', [
        'userHasItems' => $userHasItems,
        'items' => $items, // La collection filtrée et paginée existante
        // ... autres variables
    ]);
    ```

### Étape 2 : Mise à jour de la Vue Principale du Dashboard

**Fichier à modifier :** `pifpaf/resources/views/dashboard.blade.php`

La vue principale doit utiliser la nouvelle variable pour conditionner l'affichage global.

1.  **Modifier la condition d'affichage :** Remplacez la condition `@if($items->isEmpty())` par `@if(!$userHasItems)`.
    ```blade
    {{-- Avant --}}
    @if($items->isEmpty())
        <x-dashboard.empty-dashboard />
    @else
        <x-dashboard.annonces-list :items="$items" />
    @endif

    {{-- Après --}}
    @if(!$userHasItems)
        <x-dashboard.empty-dashboard />
    @else
        <x-dashboard.annonces-list :items="$items" />
    @endif
    ```
    Cela garantit que le composant `annonces-list` (qui contient les filtres) est toujours rendu tant que l'utilisateur a au moins une annonce.

### Étape 3 : Ajustement du Composant de la Liste d'Annonces

**Fichier à modifier :** `pifpaf/resources/views/components/dashboard/annonces-list.blade.php`

Ce composant affichera désormais toujours les onglets de filtre, mais affichera un message si la liste *filtrée* est vide.

1.  **Conditionner l'affichage de la liste :** Encadrez la liste des annonces (les balises `<table>` et `<div>` pour le mobile) avec une condition qui vérifie si la collection `$items` (qui est maintenant la collection filtrée) est vide.
2.  **Afficher un message contextuel :** Si `$items` est vide, affichez un message clair à l'utilisateur.

    ```blade
    {{-- La navigation par onglets reste ici, sans condition --}}
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        ...
    </nav>
    </div>

    {{-- Ajouter cette nouvelle logique --}}
    @if ($items->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500">Aucune annonce ne correspond à ce filtre.</p>
        </div>
    @else
        {{-- Mettre tout le contenu existant (table desktop et cartes mobile) ici --}}
        <!-- Vue Tableau pour Desktop -->
        <div class="hidden sm:block overflow-x-auto">
            ...
        </div>

        <!-- Vue Cartes pour Mobile -->
        <div class="sm:hidden space-y-4">
            ...
        </div>
    @endif
    ```

## 4. Validation

Pour valider le correctif :
1.  Connectez-vous avec un utilisateur qui possède plusieurs annonces (par exemple, toutes avec le statut "En ligne").
2.  Naviguez vers le tableau de bord.
3.  Cliquez sur l'onglet de filtre "Vendu".
4.  **Résultat Attendu :** Les onglets de filtre doivent rester visibles, et le message "Aucune annonce ne correspond à ce filtre" doit s'afficher dans la zone de contenu.
5.  Cliquez sur l'onglet "En ligne".
6.  **Résultat Attendu :** Les annonces de l'utilisateur doivent s'afficher correctement.
