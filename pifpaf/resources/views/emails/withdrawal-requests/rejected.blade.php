<x-mail::message>
# Demande de virement rejetée

Bonjour {{ $withdrawalRequest->user->name }},

Nous vous informons que votre demande de virement d'un montant de **{{ number_format($withdrawalRequest->amount, 2, ',', ' ') }} €** a été rejetée.

Le montant correspondant a été re-crédité sur votre portefeuille Pifpaf.

Pour toute question, n'hésitez pas à contacter notre support.

<x-mail::button :url="route('wallet.show')">
Accéder à mon portefeuille
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
