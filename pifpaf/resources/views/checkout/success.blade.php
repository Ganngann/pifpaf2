<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <h3 class="text-2xl font-bold text-green-600 mb-4">{{ __('Paiement effectué avec succès !') }}</h3>

                    <div class="mt-6">
                        <h4 class="text-lg font-semibold">{{ __('Résumé de votre commande') }}</h4>
                        <div class="mt-4 text-left inline-block">
                            <p><strong>{{ __('Article:') }}</strong> {{ $transaction->offer->item->title }}</p>
                            <p><strong>{{ __('Montant total payé:') }}</strong> {{ number_format($transaction->amount, 2, ',', ' ') }} €</p>
                            <p><strong>{{ __('Vendeur:') }}</strong> {{ $transaction->offer->item->user->name }}</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-lg font-semibold">{{ __('Prochaines étapes') }}</h4>
                        <div class="mt-4 text-gray-700">
                            @if ($transaction->offer->delivery_option === 'pickup')
                                <p>{{ __("Vous avez choisi le retrait sur place. Veuillez contacter le vendeur pour convenir d'un rendez-vous.") }}</p>
                            @else
                                <p>{{ __('Le vendeur a été notifié et va procéder à l\'expédition de votre article.') }}</p>
                                <p>{{ __('Vous recevrez une notification lorsque le numéro de suivi sera disponible.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('transactions.show', $transaction) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Voir les détails de ma commande') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
