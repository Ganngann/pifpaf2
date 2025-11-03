# Problèmes Connus

Ce document liste les problèmes identifiés dans la suite de tests qui n'ont pas encore été résolus.

## Suite de tests PHPUnit

-   **Problème :** L'exécution de la suite de tests complète (`php artisan test`) se termine par un timeout après plusieurs minutes.
-   **Contournement :** Les tests individuels ou les groupes de tests s'exécutent correctement. Cela suggère un problème de performance ou une fuite de mémoire lorsque tous les tests sont lancés ensemble, potentiellement lié à l'utilisation d'une base de données SQLite sur fichier.

## Suite de tests Dusk

Les tests suivants échouent de manière persistante :

1.  **`tests/Browser/ConversationDuskTest.php`**
    -   **Erreur :** `InvalidArgumentException: Unable to locate button [Contacter le vendeur].`
    -   **Description :** Le test ne trouve pas le bouton "Contacter le vendeur" sur la page de l'article, même si le bouton semble être correctement implémenté dans la vue Blade. La condition d'affichage du bouton n'est probablement pas remplie par les données de test, malgré plusieurs tentatives de correction.

2.  **`tests/Browser/OfferDeliveryMethodTest.php`**
    -   **Erreur :** `NoSuchElementException: no such element: Unable to locate element: {"method":"css selector","selector":"body input[name="delivery_method"][value="pickup"]"}`
    -   **Description :** Le test ne trouve pas le bouton radio pour sélectionner le retrait sur place comme méthode de livraison. Ce problème persiste même après avoir modifié le test pour s'assurer que l'article de test a bien une adresse de retrait associée et que l'option est activée.

3.  **`tests/Browser/OfferFlowTest.php`**
    -   **Erreur :** `Did not see expected text [Super Article à Vendre] within element [body].`
    -   **Description :** Le test n'arrive même pas à voir le titre de l'article sur la page. C'est probablement une conséquence du même problème que le test `OfferDeliveryMethodTest` : si les options de livraison ne s'affichent pas correctement, la page de l'article n'est pas rendue comme attendu, ce qui fait échouer les assertions de base.
