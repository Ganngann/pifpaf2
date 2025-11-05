@props(['item'])

<div x-data="{ deliveryMethod: '' }">
    @if ($item->status !== \App\Enums\ItemStatus::SOLD)
        <div>
            <div class="flex items-center justify-between">
                <span class="font-bold text-3xl">{{ number_format($item->price, 2, ',', ' ') }} €</span>
                <div class="flex space-x-2">
                    @auth
                        @if(Auth::id() !== $item->user_id)
                            <form action="{{ route('offers.buyNow', $item) }}" method="POST">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $item->price }}">
                                <input type="hidden" name="delivery_method" x-model="deliveryMethod">
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded"
                                        :disabled="!deliveryMethod"
                                        :class="{ 'opacity-50 cursor-not-allowed': !deliveryMethod }">
                                    Acheter
                                </button>
                            </form>
                            <form action="{{ route('conversations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded" dusk="contact-seller-button">
                                    Contacter le vendeur
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                            Acheter
                        </a>
                    @endauth
                </div>
            </div>
            @auth
                @if(Auth::id() !== $item->user_id)
                    <div class="mt-4 p-4 border rounded-md bg-gray-50" x-show="true">
                        <h3 class="font-semibold text-gray-800 mb-2">Veuillez sélectionner un mode de livraison pour acheter :</h3>
                        <div class="space-y-2">
                            @if ($item->pickup_available)
                            <label class="flex items-center p-3 border rounded-md has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300 cursor-pointer">
                                <input type="radio" name="delivery_method_choice" value="pickup" x-model="deliveryMethod" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" dusk="delivery-method-pickup">
                                <span class="ml-3 text-sm font-medium text-gray-700">Retrait sur place</span>
                            </label>
                            @endif
                            @if ($item->delivery_available)
                            <label class="flex items-center p-3 border rounded-md has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300 cursor-pointer">
                                <input type="radio" name="delivery_method_choice" value="delivery" x-model="deliveryMethod" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" dusk="delivery-method-delivery">
                                <span class="ml-3 text-sm font-medium text-gray-700">Livraison</span>
                            </label>
                            @endif
                        </div>
                    </div>
                @endif
            @endauth
        </div>

        {{-- Section pour faire une offre --}}
        @auth
            @if(Auth::id() !== $item->user_id)
                <div class="mt-8 pt-8 border-t">
                    <h2 class="text-2xl font-bold mb-4">Faire une offre</h2>
                    <form action="{{ route('offers.store', $item) }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery_method" x-model="deliveryMethod">
                        <div class="flex items-center">
                            <input type="number" name="amount" id="amount" class="w-full px-4 py-2 border rounded-l-md" placeholder="Votre offre" required min="0.01" step="0.01">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-r-md" dusk="submit-offer-button">
                                Envoyer l'offre
                            </button>
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            @endif
        @endauth
    @else
        @php
            $soldTransaction = null;
            foreach ($item->offers as $offer) {
                if ($offer->transaction && $offer->status === 'paid' && $offer->transaction->status === 'completed') {
                    $soldTransaction = $offer->transaction;
                    break;
                }
            }
        @endphp
        <div class="flex items-center justify-between mt-6">
            <span class="font-bold text-3xl">
                @if ($soldTransaction)
                    {{ number_format($soldTransaction->amount, 2, ',', ' ') }} €
                @else
                    {{-- Fallback au cas où la transaction n'est pas trouvée --}}
                    {{ number_format($item->price, 2, ',', ' ') }} €
                @endif
            </span>
            <span class="bg-red-100 text-red-800 text-lg font-semibold mr-2 px-4 py-2 rounded-full">Article Vendu</span>
        </div>
    @endif
</div>
