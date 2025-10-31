# Fiche Technique : US-IA-2 (Validation des suggestions de l'IA)

## 1. Objectif
Après l'analyse de l'image par l'IA, pré-remplir le formulaire de création d'annonce standard avec les données récupérées, y compris l'image. L'utilisateur peut alors valider, modifier et compléter les informations avant de publier.

**Dépendance :** Cette US est la suite directe de `US-IA-1`.

---
## 2. Modifications de la Base de Données
- Aucune.

---
## 3. Implémentation Back-End (Laravel)

- **Controller (`ItemController`) :**
  - **Modifier la méthode `create()` :**
    - La méthode `create()` doit maintenant vérifier si des données d'analyse IA existent en session.
    - **Logique :**
      1.  Vérifier la présence de `session('ai_analysis_result')`.
      2.  Si les données existent :
         - Récupérer les données de la session : `$analysisResult = session('ai_analysis_result');`
         - Récupérer le chemin de l'image temporaire : `$tempImagePath = session('ai_temp_image_path');`
         - **Préparer les données pour la vue :** Passer un objet ou un tableau `$aiData` à la vue, contenant le titre, la description, la catégorie, le prix, et le chemin de l'image.
         - **Important :** "Flasher" les données pour qu'elles soient supprimées de la session après la prochaine requête. Mieux encore, les retirer explicitement de la session pour éviter qu'elles ne persistent en cas de rechargement de la page : `session()->forget(['ai_analysis_result', 'ai_temp_image_path']);`
      3.  Si aucune donnée n'existe en session, le comportement reste normal (formulaire vide).
      4.  Retourner la vue `items.create` en lui passant les données (`compact('aiData')`).

- **Controller (`ItemController`) :**
  - **Modifier la méthode `store()` :**
    - La méthode `store()` doit pouvoir gérer l'image qui ne vient pas directement d'un upload de formulaire, mais d'un chemin temporaire.
    - **Logique :**
      1.  Ajouter un champ caché dans le formulaire de création, contenant le chemin de l'image temporaire (`ai_temp_image_path`).
      2.  Dans `store()`, si `request->has('ai_temp_image_path')` :
         - Récupérer le chemin.
         - **Déplacer le fichier :** Déplacer l'image du répertoire temporaire vers son emplacement final (`storage/app/public/item_images/{item_id}/`).
         - Créer l'enregistrement `ItemImage` comme pour un upload normal.
         - Supprimer le fichier temporaire après le déplacement.
      3.  La gestion des images téléversées normalement doit toujours fonctionner.

---
## 4. Implémentation Front-End (Blade)

- **Vue (`resources/views/items/create.blade.php`) :**
  - La vue doit être modifiée pour pouvoir afficher un formulaire soit vide, soit pré-rempli.
  - **Logique des champs :**
    - Pour chaque champ (titre, description, prix), utiliser la fonction `old()` de Laravel, avec en deuxième argument la valeur issue de l'IA (si elle existe).
      ```blade
      <input type="text" name="title" value="{{ old('title', $aiData['title'] ?? '') }}">
      ```
    - Pour la catégorie (select), la logique sera similaire pour pré-sélectionner la bonne option.
  - **Gestion de l'image :**
    - Si `$aiData` contient un chemin d'image :
      - Afficher la miniature de cette image dans la galerie.
      - Ajouter un `<input type="hidden" name="ai_temp_image_path" value="{{ $aiData['image_path'] }}">`.

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - Dans `tests/Feature/AiItemCreationTest.php`
  - **`test_creation_form_is_pre_filled_with_ai_data()` :**
    - Simuler la mise en session des données d'analyse.
    - Faire une requête `GET` vers `/items/create`.
    - `assertViewHas('aiData')`.
    - `assertSee()` pour vérifier que les valeurs (titre, etc.) sont bien présentes dans le HTML de la vue.
  - **`test_item_is_created_correctly_from_ai_flow()` :**
    - Mettre en session les données ET créer un faux fichier dans le storage temporaire.
    - Simuler une requête `POST` vers `/items` avec les données pré-remplies et le chemin de l'image temporaire.
    - `assertDatabaseHas('items', ...)` et `assertDatabaseHas('item_images', ...)` pour vérifier la création.
    - `Storage::disk('public')->assertExists(...)` pour le fichier final et `assertMissing(...)` pour le fichier temporaire.
