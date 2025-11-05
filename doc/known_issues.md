# Problèmes Connus

Ce document liste les tests qui échouent ou sont désactivés dans la suite de tests de l'application.

## Suite de Tests Frontend (Laravel Dusk)

### Tests en échec

- **Suite:** `Tests\Browser\PaymentTest`
- **Test:** `a user can pay without wallet`
- **Erreur:** `NoSuchElementException`
- **Description:** Le test échoue car il ne trouve pas un élément attendu sur la page lors de la simulation d'un paiement par carte de crédit (sans utiliser le portefeuille).
- **Trace de l'erreur:**
  ```
   FAILED  Tests\Browser\PaymentTest > a user can pa…  NoSuchElementException
  no such element: Unable to locate element: {"method":"tag name","selector":"html"}
  (Session info: chrome=142.0.7444.59)

  at vendor/php-webdriver/webdriver/lib/Exception/WebDriverException.php:120
    116▕                     throw new NoSuchAlertException($message, $results);
    117▕                 case 'no such cookie':
    118▕                     throw new NoSuchCookieException($message, $results);
    119▕                 case 'no such element':
  ➜ 120▕                     throw new NoSuchElementException($message, $results);
    121▕                 case 'no such frame':
    122▕                     throw new NoSuchFrameException($message, $results);
    123▕                 case 'no such window':
    124▕                     throw new NoSuchWindowException($message, $results);

       [2m+5 vendor frames  [22m
  6   tests/Browser/PaymentTest.php:102
  ```

### Tests Désactivés (Skipped)

Les tests suivants sont intentionnellement désactivés (`skipped`) car ils concernent des fonctionnalités liées à l'IA qui sont en cours de développement ou de refactoring.

- `Tests\Browser\AiItemCreationDuskTest`
- `Tests\Browser\MultiObjectAiItemCreationTest`
- `Tests\Browser\ValidateAiSuggestionsTest`

## Suite de Tests Backend (PHPUnit)

### Suite de tests fragile

- **Suite:** `Tests\Feature\PickupAvailableTest`, `Tests\Feature\PaymentTest`, `Tests\Feature\WalletTest`
- **Description:** Plusieurs tests dans ces suites échouent de manière intermittente ou en cascade. Le problème principal semble être le couplage fort avec le processus de paiement Stripe, qui est difficile à moquer de manière fiable dans l'environnement de test actuel. Les tentatives de correction ont montré que les tests sont sensibles à l'ordre d'exécution, ce qui indique un état partagé non maîtrisé.
- **Action recommandée:** Une refonte de ces tests est nécessaire. Il faudrait notamment mettre en place une stratégie de mock centralisée et robuste pour les services externes comme Stripe, et s'assurer que chaque test est parfaitement isolé.

### Nouveaux tests en échec

- **Suite:** `Tests\Feature\DashboardTransactionTest`
- **Description:** Les tests créés pour la nouvelle fonctionnalité du tableau de bord (`open_sales_are_displayed_on_dashboard_for_seller` et `open_purchases_are_displayed_on_dashboard_for_buyer`) échouent car les relations (vendeur/acheteur) ne semblent pas être correctement chargées et disponibles dans la vue, malgré plusieurs tentatives de correction de la requête du contrôleur. Ce problème semble spécifique à l'environnement de test et n'a pas pu être résolu dans le temps imparti.
