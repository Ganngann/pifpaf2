# Epic: Amélioration du cycle de vie des litiges

## User Stories

### Conditions d'ouverture

*   **US-L01 :** En tant qu'acheteur, je veux que le bouton "Ouvrir un litige" ne soit visible et cliquable sur une transaction que si son statut est "Expédiée" ou "Remise en main propre effectuée", et ce jusqu'à 14 jours après la confirmation de réception, afin d'éviter les litiges prématurés ou trop tardifs.

*   **US-L02 :** En tant que vendeur, je veux pouvoir ouvrir un litige si l'acheteur n'a pas confirmé la réception de l'article 7 jours après la date de livraison confirmée par le transporteur, afin de ne pas bloquer indéfiniment le paiement.

*   **US-L03 :** En tant qu'utilisateur ouvrant un litige, je veux pouvoir sélectionner une raison dans une liste prédéfinie (ex: "Objet non conforme", "Jamais reçu", "Endommagé") et ajouter une description textuelle détaillée, afin de qualifier précisément le problème.

*   **US-L04 :** En tant qu'utilisateur ouvrant un litige, je veux pouvoir joindre plusieurs pièces justificatives (photos, vidéos, documents) pour appuyer ma réclamation.

### Médiation et Communication

*   **US-L05 :** En tant qu'utilisateur, lorsque l'autre partie ouvre un litige sur notre transaction, je veux recevoir une notification par email et sur le site pour en être informé immédiatement.

*   **US-L06 :** En tant qu'acheteur ou vendeur, je veux accéder à une page dédiée au litige qui contient un fil de discussion pour communiquer avec l'autre partie et avec un administrateur, afin de centraliser les échanges relatifs au litige.

*   **US-L07 :** En tant que participant à un litige, je veux recevoir une notification lorsque l'autre partie ou un administrateur ajoute un message ou une pièce jointe au fil de discussion.

*   **US-L08 :** En tant qu'utilisateur, si l'autre partie ne répond pas à un litige dans un délai de 7 jours, je veux que le litige soit automatiquement escaladé à un administrateur pour qu'il prenne le relais.

*   **US-L09 :** En tant qu'utilisateur, je veux pouvoir demander l'intervention d'un administrateur à tout moment dans le fil de discussion du litige.

### Résolution et Clôture

*   **US-L10 :** En tant qu'administrateur, je veux consulter une vue détaillée du litige incluant le fil de discussion complet et toutes les pièces jointes pour prendre une décision éclairée.

*   **US-L11 :** En tant qu'administrateur, lorsque je résous un litige en faveur de l'acheteur, je veux que le système crée automatiquement une écriture de type `REFUND_DISPUTE` dans son `WalletHistory` au lieu de modifier directement son solde, afin de garantir l'intégrité comptable.

*   **US-L12 :** En tant qu'administrateur, lorsque je résous un litige en faveur du vendeur, je veux que le système crée automatiquement une écriture de type `PAYOUT_DISPUTE` dans son `WalletHistory` au lieu de modifier directement son solde, afin de garantir l'intégrité comptable.

*   **US-L13 :** En tant qu'administrateur, je veux pouvoir ajouter une note de clôture expliquant ma décision, qui sera visible par les deux parties.

*   **US-L14 :** En tant qu'utilisateur, je veux recevoir une notification claire (email et site) m'informant de la résolution du litige, de la décision finale et de la justification de l'administrateur.
