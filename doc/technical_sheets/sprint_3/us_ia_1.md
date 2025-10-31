# Fiche Technique : US-IA-1 (Analyse de l'image par l'IA)

## 1. Objectif
Créer un nouveau parcours de création d'annonce où l'utilisateur téléverse une seule image, qui est ensuite envoyée à une API d'IA (Gemini) pour analyse. Le résultat de cette analyse est stocké en session pour être utilisé à l'étape suivante.

---
## 2. Modifications de la Base de Données
- Aucune.

---
## 3. Implémentation Back-End (Laravel)

- **Route :**
  - Créer deux nouvelles routes pour ce parcours :
    - `GET /items/create-with-ai` -> `AiItemController@showUploadForm`
    - `POST /items/create-with-ai` -> `AiItemController@analyzeImage`

- **Nouveau Controller (`AiItemController`) :**
  - ```bash
    php artisan make:controller AiItemController
    ```
  - **Action `showUploadForm()` :**
    - Retourne simplement la vue `items.create-with-ai`.
  - **Action `analyzeImage(Request $request)` :**
    - **Validation :** Valider que la requête contient bien un fichier image.
    - **Logique :**
      1.  Récupérer le fichier image.
      2.  **Service d'IA :** Créer une classe de service dédiée (`app/Services/GeminiService.php`) pour encapsuler la logique d'appel à l'API de Gemini. Cela rend le contrôleur plus propre et la logique réutilisable.
      3.  Le service prendra le contenu de l'image en base64 et un prompt (ex: "Décris cet objet pour un site de seconde main, suggère un titre, une description, une catégorie parmi [liste des catégories] et un prix.").
      4.  Appeler ce service depuis le contrôleur : `$analysisResult = $geminiService->analyze($request->file('image'));`
      5.  Le service doit retourner une structure de données normalisée (ex: un DTO ou un tableau associatif `['title' => ..., 'description' => ..., 'category' => ..., 'price' => ...]`).
      6.  Stocker temporairement l'image téléversée dans le `storage` (ex: `storage/app/temp/`).
      7.  Stocker le chemin de l'image temporaire et le résultat de l'analyse dans la session de l'utilisateur :
         ```php
         session([
             'ai_analysis_result' => $analysisResult,
             'ai_temp_image_path' => $tempPath,
         ]);
         ```
    - **Redirection :** Rediriger l'utilisateur vers le formulaire de création manuelle : `return redirect()->route('items.create');`

- **Service (`GeminiService`) :**
  - La classe contiendra une méthode `analyze(UploadedFile $image)`.
  - Elle utilisera le client HTTP de Laravel (`Http::`) pour envoyer la requête à l'API de Gemini.
  - Elle formatera la requête avec la clé d'API (stockée dans `.env`), l'image en base64, et le prompt.
  - Elle parsera la réponse JSON de l'API pour extraire les informations et les retourner sous forme de tableau.

---
## 4. Implémentation Front-End (Blade & JS)

- **Nouvelle Vue (`resources/views/items/create-with-ai.blade.php`) :**
  - Doit être très simple et épurée pour se concentrer sur l'action unique.
  - Utilise le layout `<x-app-layout>`.
  - Contient un formulaire `multipart/form-data` qui pointe vers la route `POST /items/create-with-ai`.
  - Un seul champ `<input type="file" name="image">`.
  - **Mobile-first :** L'input doit proposer "Prendre une photo" ou "Choisir depuis la galerie".
- **JavaScript :**
  - Un script pour améliorer l'UX :
    - À la soumission du formulaire, désactiver le bouton et afficher un indicateur de chargement / spinner pour faire patienter l'utilisateur pendant l'analyse.

---
## 5. Tests à Rédiger

- **Test d'Intégration (Feature Test) :**
  - `tests/Feature/AiItemCreationTest.php`
  - **`test_user_can_upload_image_for_analysis()` :**
    - **Mocking :** Simuler le service `GeminiService` pour qu'il ne fasse pas d'appel HTTP réel. Le faire retourner une réponse contrôlée.
      ```php
      $this->mock(GeminiService::class, function ($mock) {
          $mock->shouldReceive('analyze')->andReturn([...]);
      });
      ```
    - Simuler une requête `POST` avec un faux fichier image vers `/items/create-with-ai`.
    - `assertRedirect(route('items.create'))`.
    - `assertSessionHas('ai_analysis_result')`.
  - **`test_analysis_fails_if_no_image_is_provided()` :**
    - Envoyer une requête `POST` sans fichier.
    - `assertSessionHasErrors('image')`.
