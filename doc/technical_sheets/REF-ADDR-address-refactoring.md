# Fiche Technique : REF-ADDR - Refactorisation des Adresses

**Objectif :** Unifier les modèles `PickupAddress` et `ShippingAddress` en un seul modèle `Address` polymorphe.

**Motivation :**
-   Réduire la duplication de code et de schéma de base de données.
-   Simplifier la maintenance et l'ajout de futures fonctionnalités liées aux adresses (ex: adresse par défaut, carnet d'adresses unifié).
-   Centraliser la logique métier (ex: géocodage).

---

## Consignes Clés pour l'Exécution

*Le succès de cette refactorisation repose sur une exécution disciplinée. Les consignes suivantes sont impératives.*

1.  **Une User Story = Une Pull Request.**
    -   Chaque US du plan doit faire l'objet d'une branche et d'une Pull Request (PR) dédiées. Ne mélangez jamais plusieurs US dans une seule PR.

2.  **Respect Impératif de la Procédure `agent_mission_template.md` pour CHAQUE US.**
    -   Ce document est la checklist à suivre pour chaque étape : Analyse -> Plan d'action spécifique à l'US -> TDD -> Clôture.

3.  **La Validation par les Tests est Non-Négociable.**
    -   À la fin de chaque US, l'intégralité des suites de tests (`php artisan test` et `php artisan dusk`) doit passer.
    -   Les "Flaky Tests" (tests instables) doivent être identifiés et si possible stabilisés.

4.  **Confirmation sur la Gestion des Données.**
    -   Ce plan assume que **la perte des données d'adresses existantes est acceptable**. Si ce n'est plus le cas, le plan doit être amendé pour inclure des scripts de migration de données.

5.  **Communication en Cas d'Imprévu.**
    -   Si une difficulté imprévue ou une meilleure approche apparaît, il est préférable de s'arrêter pour discuter et ajuster le plan.

---

## Suivi de l'Avancement

- [ ] **US REF-ADDR-1 :** Mettre en Place la Structure `Address` Unifiée
- [ ] **US REF-ADDR-2 :** Dupliquer la Logique pour `PickupAddress`
- [ ] **US REF-ADDR-3 :** Migrer `Item` vers le Modèle `Address`
- [ ] **US REF-ADDR-4 :** Finaliser la Transition et Nettoyer `PickupAddress`
- [ ] **US REF-ADDR-5 :** Migrer la Logique de `ShippingAddress`
- [ ] **US REF-ADDR-6 :** Nettoyage Final de `ShippingAddress`

---

## Plan d'Action Incrémental

Ce plan est décomposé en User Stories atomiques. Chaque US représente une étape de travail indépendante qui doit aboutir à une Pull Request valide, avec une suite de tests entièrement fonctionnelle (`php artisan test` et `php artisan dusk`).

### **Épic : REF-ADDR - Refactorisation des Adresses**

#### **US REF-ADDR-1 : Mettre en Place la Structure `Address` Unifiée**

*Objectif : Créer la nouvelle structure de données et les modèles sans modifier le code applicatif existant. L'application doit rester 100% fonctionnelle.*

1.  **Créer la Migration :**
    -   Générer une migration `create_addresses_table`.
    -   Définir le schéma pour la table `addresses` :
        -   `id`, `timestamps`
        -   `foreignId('user_id')`
        -   `enum('type', ['pickup', 'shipping'])`
        -   `string('name')`
        -   `string('street')`, `string('city')`, `string('postal_code')`, `string('country')->nullable()`
        -   `decimal('latitude', 10, 7)->nullable()`, `decimal('longitude', 10, 7)->nullable()`

2.  **Créer le Modèle et ses Dépendances :**
    -   Générer le modèle `App\Models\Address` avec sa `Factory` et sa `Policy` (`php artisan make:model Address -fp`).
    -   Configurer la propriété `$fillable` dans le modèle `Address`.
    -   Implémenter la relation `user()` (`belongsTo(User::class)`).

3.  **Mettre à Jour le Modèle `User` :**
    -   Ajouter la nouvelle relation `addresses()` (`hasMany(Address::class)`).

4.  **Validation :**
    -   Exécuter les migrations (`php artisan migrate`).
    -   Lancer l'intégralité des tests (`php artisan test` et `php artisan dusk`). **Aucun test ne doit échouer.**

---

#### **US REF-ADDR-2 : Dupliquer la Logique pour `PickupAddress`**

*Objectif : Faire coexister l'ancien et le nouveau système pour les adresses de retrait. Le nouveau système est alimenté en parallèle de l'ancien.*

1.  **Créer une Migration de Liaison :**
    -   Générer une migration pour ajouter une colonne `address_id` (clé étrangère `nullable` vers `addresses`) à la table `pickup_addresses`.

2.  **Modifier `PickupAddressController` pour la Double Écriture :**
    -   **`store()` :**
        1.  Valider les données.
        2.  Créer une entrée dans la table `addresses` avec `type = 'pickup'`.
        3.  Créer une entrée dans la table `pickup_addresses` en y stockant l'ID de la nouvelle `Address` dans `address_id`.
    -   **`update()` :**
        1.  Mettre à jour l'enregistrement `PickupAddress`.
        2.  Retrouver et mettre à jour l'enregistrement `Address` correspondant via `address_id`.
    -   **`destroy()` :**
        1.  Supprimer l'enregistrement `Address` correspondant.
        2.  Supprimer l'enregistrement `PickupAddress`.

3.  **Validation :**
    -   Exécuter les migrations.
    -   Lancer tous les tests. Ils doivent continuer de passer car aucune logique de lecture n'a encore été modifiée.
    -   **Manuellement (ou via un nouveau test) :** Vérifier que la création/modification/suppression d'une `PickupAddress` est bien répercutée dans la table `addresses`.

---

#### **US REF-ADDR-3 : Migrer `Item` vers le Modèle `Address`**

*Objectif : Remplacer la dépendance du modèle `Item` de `PickupAddress` vers `Address`.*

1.  **Créer une Migration de Schéma :**
    -   Générer une migration pour la table `items`.
    -   Renommer la colonne `pickup_address_id` en `address_id`.
    -   Mettre à jour la contrainte de clé étrangère pour qu'elle pointe vers la table `addresses`.

2.  **Mettre à Jour le Modèle `Item` :**
    -   Modifier la propriété `$fillable` pour remplacer `pickup_address_id` par `address_id`.
    -   Renommer la relation `pickupAddress()` en `address()` et la faire pointer vers `App\Models\Address`.

3.  **Mettre à Jour la `ItemFactory` :**
    -   Remplacer `pickup_address_id` par `address_id`.
    -   Utiliser `Address::factory(['type' => 'pickup'])` pour générer l'adresse associée.

4.  **Mettre à Jour `ItemController` :**
    -   Remplacer toutes les occurrences de `pickup_address_id` par `address_id` (validation, assignation).
    -   Utiliser la nouvelle relation `address()` au lieu de `pickupAddress()`.
    -   Dans la méthode `create()` et `edit()`, charger les adresses via `Auth::user()->addresses()->where('type', 'pickup')->get()`.
    -   Dans la méthode `welcome()`, mettre à jour la requête du filtre par distance pour utiliser la table `addresses`.

5.  **Mettre à Jour les Vues :**
    -   Dans les formulaires de création/édition d'annonces (`items/create.blade.php`, `items/edit.blade.php`), s'assurer que le champ `select` pour l'adresse a pour `name="address_id"`.

6.  **Mettre à Jour les Tests :**
    -   Rechercher toutes les occurrences de `pickup_address_id` dans le répertoire `tests/` et les remplacer par `address_id`.
    -   Adapter les factories dans les tests pour créer des `Address` au lieu de `PickupAddress`.

7.  **Validation :**
    -   Exécuter les migrations.
    -   Lancer tous les tests. **Tous les tests doivent passer.**

---

#### **US REF-ADDR-4 : Finaliser la Transition et Nettoyer `PickupAddress`**

*Objectif : Supprimer complètement l'ancien système d'adresses de retrait.*

1.  **Créer un `AddressController` Unifié :**
    -   Générer `AddressController` (`php artisan make:controller AddressController --model=Address --resource`).
    -   Copier la logique CRUD de `PickupAddressController` dans `AddressController`, en l'adaptant pour gérer les `Address` de type `pickup`.
    -   Mettre à jour les `Policies` si nécessaire.

2.  **Mettre à Jour les Routes (`web.php`) :**
    -   Faire pointer la route ressource `profile/addresses` vers le nouveau `AddressController`.

3.  **Supprimer les Anciens Fichiers :**
    -   Supprimer `app/Http/Controllers/PickupAddressController.php`.
    -   Supprimer `app/Models/PickupAddress.php`.
    -   Supprimer `database/factories/PickupAddressFactory.php`.
    -   Supprimer `app/Policies/PickupAddressPolicy.php`.
    -   Supprimer les tests spécifiques à `PickupAddressController`.

4.  **Nettoyer le Modèle `User` :**
    -   Supprimer la relation `pickupAddresses()`.

5.  **Créer une Migration de Nettoyage :**
    -   Générer une migration pour supprimer la table `pickup_addresses`.

6.  **Validation :**
    -   Exécuter `composer dump-autoload`.
    -   Exécuter les migrations.
    -   Lancer tous les tests. **Tous les tests doivent passer.**

---

#### **US REF-ADDR-5 : Migrer la Logique de `ShippingAddress`**

*Objectif : Intégrer la gestion des adresses de livraison dans le nouveau système unifié.*

1.  **Adapter `AddressController` :**
    -   Modifier les méthodes `create` et `store` pour gérer la création d'adresses de type `shipping` (sans géocodage).
    -   Modifier les méthodes `edit` et `update` pour gérer la modification des `Address` de type `shipping`.
    -   La méthode `index` doit maintenant retourner une vue unique affichant les deux types d'adresses (ce qui est déjà le cas).

2.  **Mettre à Jour les Routes (`web.php`) :**
    -   Supprimer la route ressource pour `profile/shipping-addresses`.
    -   S'assurer que les formulaires de création/édition d'adresses de livraison pointent vers les routes de `AddressController`.

3.  **Migrer `Transaction` vers `Address` :**
    -   *Suivre la même logique que pour `Item` (US REF-ADDR-3)* :
        -   Créer une migration pour renommer `shipping_address_id` en `address_id` dans la table `transactions`.
        -   Mettre à jour le modèle `Transaction` (relation, `$fillable`).
        -   Mettre à jour `TransactionFactory`.
        -   Mettre à jour les contrôleurs et tests qui référencent `shipping_address_id`.

---

#### **US REF-ADDR-6 : Nettoyage Final de `ShippingAddress`**

*Objectif : Supprimer les derniers vestiges de l'ancien système.*

1.  **Supprimer les Anciens Fichiers :**
    -   `app/Http/Controllers/ShippingAddressController.php`
    -   `app/Models/ShippingAddress.php`
    -   `database/factories/ShippingAddressFactory.php`
    -   `app/Policies/ShippingAddressPolicy.php`
    -   Les tests spécifiques.

2.  **Nettoyer le Modèle `User` :**
    -   Supprimer la relation `shippingAddresses()`.

3.  **Créer une Migration de Nettoyage :**
    -   Générer une migration pour supprimer la table `shipping_addresses`.

4.  **Validation Finale :**
    -   `composer dump-autoload`.
    -   Lancer tous les tests. **Tous les tests doivent passer.** La refactorisation est terminée.
