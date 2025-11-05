@props(['item'])

@if ($item->status !== \App\Enums\ItemStatus::SOLD)
    <div>
        {{-- Section Prix et Actions --}}
        <div class="bg-gray-50 p-4 rounded-lg border">
            <h2 class="text-lg font-semibold mb-3">Prix et Actions</h2>
            <div class="flex items-center justify-between mb-4">
                <span class="font-bold text-3xl text-gray-800">{{ number_format($item->price, 2, ',', ' ') }} €</span>
            </div>
            <div class="grid grid-cols-1 gap-3">
                @auth
                    @if(Auth::id() !== $item->user_id)
                        <form action="{{ route('offers.buyNow', $item) }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="amount" value="{{ $item->price }}">
                            <input type="hidden" name="delivery_method" x-model="deliveryMethod">
                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-md transition-opacity duration-200"
                                    :disabled="!deliveryMethod"
                                    :class="{ 'opacity-50 cursor-not-allowed': !deliveryMethod }">
                                Acheter
                            </button>
                        </form>
                        <form action="{{ route('conversations.store') }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-md" dusk="contact-seller-button">
                                Contacter le vendeur
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-600">Ceci est l'une de vos annonces. Vous ne pouvez pas l'acheter.</p>
                    @endif
                @else
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-md">
                        Acheter
                    </a>
                    <form action="{{ route('conversations.store') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                        <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-md" dusk="contact-seller-button">
                            Contacter le vendeur
                        </button>
                    </form>
                @endauth
            </div>
            @auth
                @if(Auth::id() !== $item->user_id)
                    <p x-show="!deliveryMethod" class="text-red-500 text-xs mt-2">Veuillez sélectionner un mode de livraison.</p>
                @endif
            @endauth
        </div>

        {{-- Section Faire une offre --}}
        @auth
            @if(Auth::id() !== $item->user_id)
                <div class="mt-4 bg-gray-50 p-4 rounded-lg border">
                    <h2 class="text-lg font-semibold mb-3">Faire une offre</h2>
                    <form action="{{ route('offers.store', $item) }}" method="POST">
                        @csrf
                        <input type="hidden" name="delivery_method" x-model="deliveryMethod">
                        <div class="flex items-center">
                            <input type="number" name="amount" id="amount" class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500" placeholder="Votre offre (€)" required min="0.01" step="0.01">
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-r-md transition-opacity duration-200"
                                    :disabled="!deliveryMethod"
                                    :class="{ 'opacity-50 cursor-not-allowed': !deliveryMethod }">
                                Envoyer
                            </button>
                        </div>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            @endif
        @endauth
    </div>
@else
    {{-- Section si l'article est vendu --}}
    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
        <div class="flex items-center justify-between">
            @php
                $soldTransaction = $item->offers->firstWhere('transaction.status', 'completed')?->transaction;
            @endphp
            <span class="font-bold text-3xl text-red-800">
                @if ($soldTransaction)
                    {{ number_format($soldTransaction->amount, 2, ',', ' ') }} €
                @else
                    {{ number_format($item->price, 2, ',', ' ') }} €
                @endif
            </span>
            <span class="bg-red-100 text-red-800 text-lg font-semibold px-4 py-2 rounded-full">Article Vendu</span>
        </div>
    </div>
@endif
