<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Récapitulatif de la commande') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Veuillez vérifier les détails de votre commande avant de procéder au paiement.
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Colonne de gauche : Détails de l'article -->
                        <div>
                            <h4 class="font-semibold text-gray-800">Article</h4>
                            <div class="mt-2 flex">
                                <img src="{{ optional($offer->item->primary_image)->path ? asset('storage/' . $offer->item->primary_image->path) : asset('images/placeholder.jpg') }}" alt="{{ $offer->item->title }}" class="w-24 h-24 object-cover rounded-md">
                                <div class="ml-4">
                                    <p class="font-bold">{{ $offer->item->title }}</p>
                                    <p class="text-sm text-gray-600">Vendu par : {{ $offer->item->user->name }}</p>
                                    <p class="text-lg font-bold text-gray-800 mt-2">{{ number_format($offer->amount, 2, ',', ' ') }} €</p>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne de droite : Détails de la livraison et financier -->
                        <div>
                            <h4 class="font-semibold text-gray-800">Livraison</h4>
                            <div class="mt-2">
                                @if($offer->delivery_method == 'pickup')
                                    <p><strong>Remise en main propre</strong></p>
                                    @if($offer->item->address)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <strong>Adresse de retrait :</strong><br>
                                            {{ $offer->item->address->street }},<br>
                                            {{ $offer->item->address->postal_code }} {{ $offer->item->address->city }}
                                        </p>
                                    @else
                                        <div class="text-sm text-orange-600 mt-1 p-4 border border-orange-300 rounded-md bg-orange-50">
                                            <p><strong>Adresse de retrait non spécifiée.</strong></p>
                                            <p>Le vendeur n'a pas encore défini d'adresse de retrait pour cet article.</p>
                                        </div>
                                    @endif
                                @else
                                    <p>Livraison standard</p>
                                    @if($shippingAddress)
                                        <div class="text-sm text-gray-600 mt-1">
                                            <p>
                                                <strong>Adresse de livraison :</strong><br>
                                                {{ $shippingAddress->name }}<br>
                                                {{ $shippingAddress->street }},<br>
                                                {{ $shippingAddress->postal_code }} {{ $shippingAddress->city }},<br>
                                                {{ $shippingAddress->country }}
                                            </p>
                                            <a href="{{ route('profile.edit') }}#shipping-addresses" class="text-indigo-600 hover:text-indigo-900 text-xs mt-2 inline-block">
                                                Modifier l'adresse
                                            </a>
                                        </div>
                                    @else
                                        <div class="text-sm text-red-600 mt-1 p-4 border border-red-300 rounded-md bg-red-50">
                                            <p><strong>Aucune adresse de livraison configurée.</strong></p>
                                            <a href="{{ route('profile.edit') }}#shipping-addresses" class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                                Veuillez en ajouter une à votre profil pour continuer.
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <h4 class="font-semibold text-gray-800 mt-6">Récapitulatif financier</h4>
                            <div class="mt-2 space-y-1">
                                <div class="flex justify-between">
                                    <span>Prix de l'article :</span>
                                    <span>{{ number_format($offer->amount, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Frais de livraison :</span>
                                    <span>0,00 €</span> {{-- À ajuster si la livraison a un coût --}}
                                </div>
                                <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                                    <span>Total à payer :</span>
                                    <span>{{ number_format($offer->amount, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bouton d'action -->
                    <div class="mt-8 text-right">
                        <form action="{{ route('payment.create', $offer) }}" method="GET">
                            @if($offer->delivery_method == 'delivery' && $shippingAddress)
                                <input type="hidden" name="address_id" value="{{ $shippingAddress->id }}">
                            @endif
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Procéder au paiement
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
