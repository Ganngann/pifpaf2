<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Transaction') }}
            </h2>
            <a href="{{ url()->previous() }}" class="text-sm text-blue-500 hover:underline">
                &larr; Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Statut et Date -->
                    <div class="mb-6 pb-4 border-b">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-500">Transaction #{{ $transaction->id }}</span>
                                <h3 class="text-lg font-semibold">Le {{ $transaction->created_at->format('d/m/Y à H:i') }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-500">Statut</span>
                                <p class="font-semibold px-3 py-1 bg-gray-200 text-gray-800 rounded-full text-sm inline-block">
                                    {{ $transaction->status }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de l'article -->
                    <div class="flex flex-col sm:flex-row gap-6 mb-6">
                        <div class="sm:w-1/3">
                            <a href="{{ route('items.show', $transaction->offer->item) }}">
                                <img src="{{ optional($transaction->offer->item->primaryImage)->path ? asset('storage/' . $transaction->offer->item->primaryImage->path) : 'https://via.placeholder.com/300' }}"
                                     alt="{{ $transaction->offer->item->title }}" class="rounded-lg w-full object-cover aspect-square">
                            </a>
                        </div>
                        <div class="sm:w-2/3">
                            <h2 class="text-2xl font-bold mb-2">
                                <a href="{{ route('items.show', $transaction->offer->item) }}" class="hover:underline">
                                    {{ $transaction->offer->item->title }}
                                </a>
                            </h2>
                            @if(Auth::id() === $transaction->offer->user_id) <!-- L'utilisateur est l'acheteur -->
                                <p class="text-gray-600 mb-4">
                                    Vendu par :
                                    <a href="{{ route('profile.show', $transaction->offer->item->user) }}" class="text-blue-500 hover:underline">
                                        {{ $transaction->offer->item->user->name }}
                                    </a>
                                </p>
                            @else <!-- L'utilisateur est le vendeur -->
                                <p class="text-gray-600 mb-4">
                                    Acheté par :
                                    <a href="{{ route('profile.show', $transaction->offer->user) }}" class="text-blue-500 hover:underline">
                                        {{ $transaction->offer->user->name }}
                                    </a>
                                </p>
                            @endif
                            <p class="text-gray-700">{{ $transaction->offer->item->description }}</p>
                        </div>
                    </div>

                    <!-- Récapitulatif financier -->
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-lg mb-2">Récapitulatif du paiement</h4>
                        <div class="space-y-2 text-gray-700">
                            <div class="flex justify-between">
                                <span>Prix de l'article</span>
                                <span>{{ number_format($transaction->offer->amount, 2, ',', ' ') }} €</span>
                            </div>
                             <div class="flex justify-between">
                                <span>Frais de service</span>
                                <span>0,00 €</span>
                            </div>
                            <div class="flex justify-between font-bold text-black border-t pt-2 mt-2">
                                <span>Total payé</span>
                                <span>{{ number_format($transaction->amount, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de livraison -->
                    @if($transaction->shippingAddress)
                        <div class="border-t pt-4 mt-6">
                             <h4 class="font-semibold text-lg mb-2">Informations de livraison</h4>
                             <div class="text-gray-700">
                                 <p>{{ $transaction->shippingAddress->name }}</p>
                                 <p>{{ $transaction->shippingAddress->address_line_1 }}</p>
                                 @if($transaction->shippingAddress->address_line_2)
                                     <p>{{ $transaction->shippingAddress->address_line_2 }}</p>
                                 @endif
                                 <p>{{ $transaction->shippingAddress->postal_code }} {{ $transaction->shippingAddress->city }}</p>
                                 <p>{{ $transaction->shippingAddress->country }}</p>
                             </div>
                             @if($transaction->tracking_code)
                                <div class="mt-4">
                                    <p><strong>Code de suivi :</strong> {{ $transaction->tracking_code }}</p>
                                </div>
                             @endif
                              @if($transaction->label_url)
                                <div class="mt-4">
                                    <a href="{{ $transaction->label_url }}" target="_blank" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                        Voir l'étiquette d'envoi
                                    </a>
                                </div>
                             @endif
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="border-t pt-4 mt-6 flex justify-end gap-2">
                        @if(Auth::id() === $transaction->offer->item->user_id) <!-- L'utilisateur est le vendeur -->
                            @if(is_null($transaction->tracking_code) && $transaction->shippingAddress)
                                <form action="{{ route('transactions.ship', $transaction) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                        Créer l'étiquette d'envoi
                                    </button>
                                </form>
                            @endif
                        @else <!-- L'utilisateur est l'acheteur -->
                            @if ($transaction->status === 'payment_received')
                                <form action="{{ route('transactions.confirm-reception', $transaction) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                                        Confirmer la réception
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- @if ($transaction->status === 'completed' && !$transaction->review && Auth::id() === $transaction->offer->user_id)
                             <a href="#" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600">
                                Laisser un avis
                            </a>
                        @endif --}}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
