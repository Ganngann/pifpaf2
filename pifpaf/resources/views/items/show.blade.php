<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if ($item->images->isNotEmpty())
                <div x-data="{ mainImageUrl: '{{ asset('storage/' . $item->primaryImage->path) }}' }">
                    <!-- Image principale -->
                    <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                        <img :src="mainImageUrl" alt="{{ $item->title }}" class="w-full h-full object-contain">
                    </div>

                    <!-- Galerie de miniatures -->
                    @if($item->images->count() > 1)
                        <div class="flex space-x-2 p-4 bg-gray-100 overflow-x-auto">
                            @foreach($item->images as $image)
                                @php
                                    $imageUrl = asset('storage/' . $image->path);
                                @endphp
                                <img src="{{ $imageUrl }}"
                                     alt="Miniature de {{ $item->title }}"
                                     class="w-24 h-24 object-cover rounded-md cursor-pointer border-2 hover:border-blue-500"
                                     :class="{ 'border-blue-500': mainImageUrl === '{{ $imageUrl }}' }"
                                     @click="mainImageUrl = '{{ $imageUrl }}'">
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Aucune image disponible</span>
                </div>
            @endif
            <div class="p-8">
                <h1 class="text-4xl font-bold mb-2" dusk="item-title">{{ $item->title }}</h1>
                <div class="mb-6">
                    <span class="text-gray-500">Vendu par :</span>
                    <a href="{{ route('profile.show', $item->user) }}" class="font-semibold text-blue-600 hover:underline">
                        {{ $item->user->name }}
                    </a>
                </div>
                <p class="text-gray-600 mb-8">{{ $item->description }}</p>
                <div x-data="{ deliveryMethod: '' }">
                <!-- Modes de livraison -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-3">Modes de livraison</h2>
                    <div class="space-y-4">
                        @if ($item->pickup_available && $item->pickupAddress)
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-gray-800">Retrait sur place</p>
                                        <p class="text-sm text-gray-500">À {{ $item->pickupAddress->city }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($item->delivery_available)
                            <div class="p-4 bg-gray-50 rounded-lg border">
                                <div class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-gray-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2-2h8a1 1 0 001-1zM21 11V5a2 2 0 00-2-2H9.5a2 2 0 00-2 2v2" />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-gray-800">Livraison</p>
                                        <p class="text-sm text-gray-500">Envoi par colis postal.</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

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

            </div>
        </div>
    </div>
</x-main-layout>
