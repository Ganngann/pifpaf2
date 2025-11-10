# User Stories - Gestion des Virements

## Épopée : Retrait des fonds du portefeuille

Cette épopée couvre la capacité d'un utilisateur (vendeur) à retirer les fonds de son portefeuille vers son compte bancaire personnel.

---

### US-W1 : Enregistrement des informations bancaires

**En tant que** vendeur,
**Je veux** pouvoir enregistrer et gérer mes informations de paiement (coordonnées bancaires) de manière sécurisée,
**Afin de** pouvoir recevoir les fonds de mon portefeuille.

**Critères d'acceptation :**
- Un formulaire dédié permet de saisir les informations bancaires (IBAN, etc.).
- Les informations sont stockées de manière sécurisée (chiffrées en base de données).
- Je peux modifier ou supprimer mes informations bancaires.
- Le système valide le format des informations saisies (ex: format IBAN).

---

### US-W2 : Demande de virement

**En tant que** vendeur,
**Je veux** pouvoir initier une demande de virement depuis la page de mon portefeuille,
**Afin de** transférer tout ou partie de mon solde disponible vers mon compte bancaire enregistré.

**Critères d'acceptation :**
- Sur la page de mon portefeuille, un bouton "Effectuer un virement" est visible.
- Un formulaire me permet de spécifier le montant que je souhaite retirer.
- Le montant demandé ne peut pas dépasser mon solde disponible.
- Une fois la demande soumise, un enregistrement de "demande de virement" est créé avec le statut "En attente".
- Le montant demandé est "gelé" dans mon portefeuille : il n'est plus disponible pour d'autres transactions, mais n'est pas encore débité.
- Je reçois une confirmation que ma demande a bien été prise en compte.

---

### US-W3 : Suivi du statut d'une demande de virement

**En tant que** vendeur,
**Je veux** voir l'historique de mes demandes de virement et leur statut (ex: En attente, Approuvé, Payé, Rejeté, Échoué),
**Afin de** suivre l'avancement de mes transferts de fonds.

**Critères d'acceptation :**
- Une section dans mon portefeuille liste toutes mes demandes de virement passées et présentes.
- Pour chaque demande, je peux voir le montant, la date et le statut actuel.
- Les statuts sont clairs pour l'utilisateur : "En attente", "Approuvé" (en attente de paiement), "Payé", "Rejeté", "Échoué".
- Si une demande échoue ou est rejetée, je peux voir la raison (si disponible).

---

### US-W4 : Gestion et validation des demandes de virement (Admin)

**En tant qu'** administrateur,
**Je veux** accéder à un tableau de bord listant toutes les demandes de virement,
**Afin de** pouvoir les valider, les rejeter et suivre leur état.

**Critères d'acceptation :**
- Une interface d'administration affiche la liste des demandes de virement, filtrable par statut (`pending`, `approved`, `paid`, etc.).
- La vue par défaut montre les demandes `pending` qui nécessitent une action.
- Je peux consulter les détails de chaque demande (utilisateur, montant, informations bancaires).
- Je dispose de boutons pour "Approuver" ou "Rejeter" une demande `pending`.
- Si je rejette une demande, je peux fournir une raison. Les fonds gelés sont alors restitués au portefeuille de l'utilisateur.
- Si j'approuve une demande, le statut passe à `approved` et la demande apparaît dans la liste des virements à exécuter.

---

### US-W5 : Traitement manuel du virement (Admin)

**En tant qu'** administrateur,
**Je veux** avoir une vue claire des virements approuvés à effectuer,
**Afin de** les exécuter manuellement depuis le compte bancaire de l'entreprise.

**Critères d'acceptation :**
- Une section du tableau de bord liste spécifiquement les demandes avec le statut `approved`.
- Cette vue affiche toutes les informations nécessaires pour effectuer le virement (Nom, IBAN, Montant).
- L'interface est conçue pour éviter les erreurs (par exemple, copier facilement les informations).

---

### US-W6 : Confirmation de paiement du virement (Admin)

**En tant qu'** administrateur,
**Je veux** pouvoir marquer un virement comme "Payé" ou "Échoué" après avoir tenté de l'exécuter,
**Afin de** finaliser le cycle de vie de la demande et de maintenir un historique précis.

**Critères d'acceptation :**
- Sur une demande `approved`, des boutons "Marquer comme Payé" et "Marquer comme Échoué" sont disponibles.
- Si je clique sur "Marquer comme Payé", le statut passe à `paid`, et les fonds sont définitivement débités du portefeuille et du solde gelé du vendeur.
- Si je clique sur "Marquer comme Échoué", je peux indiquer une raison, le statut passe à `failed`, et les fonds sont restitués au portefeuille du vendeur.
- L'utilisateur est notifié du changement de statut.

---

### US-W7 : Notifications par email

**En tant que** vendeur,
**Je veux** recevoir des notifications par email concernant mes demandes de virement,
**Afin d'**être informé des étapes importantes.

**Critères d'acceptation :**
- Je reçois un email de confirmation lorsque je soumets une demande.
- Je reçois un email lorsque ma demande est approuvée par un administrateur.
- Je reçois un email final pour m'informer si le virement a été payé avec succès ou s'il a échoué.
