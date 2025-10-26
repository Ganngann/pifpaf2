<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-96 object-cover">
            <div class="p-8">
                <h1 class="text-4xl font-bold mb-2">{{ $item->title }}</h1>
                <div class="mb-6">
                    <span class="text-gray-500">Vendu par :</span>
                    <a href="{{ route('profile.show', $item->user) }}" class="font-semibold text-blue-600 hover:underline">
                        {{ $item->user->name }}
                    </a>
                </div>
                <p class="text-gray-600 mb-8">{{ $item->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-3xl">{{ $item->price }} €</span>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                        Acheter
                    </button>
                </div>

                {{-- Section pour faire une offre --}}
                @auth
                    @if(Auth::id() !== $item->user_id)
                        <div class="mt-8 pt-8 border-t">
                            <h2 class="text-2xl font-bold mb-4">Faire une offre</h2>
                            <form action="{{ route('offers.store', $item) }}" method="POST">
                                @csrf
                                <div class="flex items-center">
                                    <input type="number" name="amount" id="amount" class="w-full px-4 py-2 border rounded-l-md" placeholder="Votre offre" required min="0.01" step="0.01">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-r-md">
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
