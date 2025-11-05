# Problèmes Connus et Tests Défaillants

Ce document centralise les problèmes connus, les tests défaillants (failures) et les tests intentionnellement ignorés (skipped) dans la suite de tests du projet Pifpaf.

L'objectif est de fournir une vue d'ensemble claire de l'état de la base de code pour éviter de perdre du temps sur des problèmes déjà identifiés et pour prioriser les corrections.

---

## Tests PHPUnit (Back-End)

*Dernière exécution : 2025-11-05*

### Tests Ignorés (Skipped)

- **Test:** `Tests\Feature\Feature\PaymentFlowTest`
  - **Raison :** Non spécifiée dans la sortie du test. Probablement désactivé pour éviter les appels API réels.
  - **Fichier :** `pifpaf/tests/Feature/Feature/PaymentFlowTest.php`

- **Test:** `Tests\Feature\PaymentTest`
  - **Raison :** "Les tests de paiement sont désactivés pour éviter les appels API réels."
  - **Fichier :** `pifpaf/tests/Feature/PaymentTest.php`

### Tests Défaillants (Failures)

*Aucun test défaillant lors de la dernière exécution.*

---

## Tests Dusk (Front-End)

*Dernière exécution : 2025-11-05*

### Tests Ignorés (Skipped)

- **Test:** `Tests\Browser\AiItemCreationDuskTest`
  - **Raison :** "Skipping AI test due to external dependency"
  - **Fichier :** `pifpaf/tests/Browser/AiItemCreationDuskTest.php`

- **Test:** `Tests\Browser\MultiObjectAiItemCreationTest`
  - **Raison :** "Skipping this test as it is flaky and depends on AI service."
  - **Fichier :** `pifpaf/tests/Browser/MultiObjectAiItemCreationTest.php`

- **Test:** `Tests\Browser\PaymentFlowTest`
  - **Raison :** "Les tests de paiement sont désactivés pour éviter les appels API réels."
  - **Fichier :** `pifpaf/tests/Browser/PaymentFlowTest.php`

- **Test:** `Tests\Browser\PaymentTest` (3 tests)
  - **Raison :** "Les tests de paiement sont désactivés pour éviter les appels API réels."
  - **Fichier :** `pifpaf/tests/Browser/PaymentTest.php`

- **Test:** `Tests\Browser\ValidateAiSuggestionsTest`
  - **Raison :** "Skipping AI validation test."
  - **Fichier :** `pifpaf/tests/Browser/ValidateAiSuggestionsTest.php`

- **Test:** `Tests\Browser\BuyerConfirmsReceptionTest`
  - **Raison :** "Les tests de paiement sont désactivés car ils dépendent de services externes non disponibles dans l'environnement de test Dusk."
  - **Fichier :** `pifpaf/tests/Browser/BuyerConfirmsReceptionTest.php`


### Tests Défaillants (Failures)

- **Test:** `Tests\Browser\PickupAddressManagementTest` > `user can navigate to addresses page`
  - **Erreur :** `Did not see expected text [Mes Adresses de Retrait] within element [body].` Le test n'a pas trouvé le titre attendu sur la page de gestion des adresses.
  - **Fichier :** `pifpaf/tests/Browser/PickupAddressManagementTest.php`

- **Test:** `Tests\Browser\PickupAddressManagementTest` > `user can add a new pickup address`
  - **Erreur :** `JavascriptErrorException: javascript error: Cannot read properties of undefined (reading 'click')`. Erreur JavaScript lors de la tentative de clic sur un élément, probablement lié au menu déroulant ou à un composant Alpine.js.
  - **Fichier :** `pifpaf/tests/Browser/PickupAddressManagementTest.php`
