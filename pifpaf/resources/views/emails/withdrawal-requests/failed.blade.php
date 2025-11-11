<x-mail::message>
# Échec de votre virement

Bonjour {{ $withdrawalRequest->user->name }},

Nous avons rencontré un problème lors du traitement de votre virement de **{{ number_format($withdrawalRequest->amount, 2, ',', ' ') }} €** vers le compte se terminant par ...{{ substr($withdrawalRequest->bankAccount->iban, -4) }}.

Le virement a échoué et le montant a été re-crédité sur votre portefeuille Pifpaf.

Nous vous invitons à vérifier vos informations bancaires et à soumettre une nouvelle demande. Si le problème persiste, veuillez contacter notre support.

<x-mail::button :url="route('wallet.show')">
Accéder à mon portefeuille
</x-mail::button>

Cordialement,<br>
L'équipe {{ config('app.name') }}
</x-mail::message>
