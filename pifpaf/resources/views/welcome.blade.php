<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Trouvez la perle rare</h1>

        <!-- Formulaire de recherche et de filtrage -->
        <div class="mb-8 p-4 bg-white rounded-lg shadow-md">
            <form action="{{ route('welcome') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-center">

                    <!-- Champ de recherche par mot-clé -->
                    <div class="sm:col-span-2 lg:col-span-2">
                        <label for="search" class="sr-only">Rechercher</label>
                        <input type="text" id="search" name="search" placeholder="Que recherchez-vous ?" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ request('search') }}">
                    </div>

                    <!-- Filtre par catégorie -->
                    <div>
                        <label for="category" class="sr-only">Catégorie</label>
                        <select id="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes les catégories</option>
                            <option value="Vêtements" @if(request('category') == 'Vêtements') selected @endif>Vêtements</option>
                            <option value="Électronique" @if(request('category') == 'Électronique') selected @endif>Électronique</option>
                            <option value="Maison" @if(request('category') == 'Maison') selected @endif>Maison</option>
                            <option value="Sport" @if(request('category') == 'Sport') selected @endif>Sport</option>
                            <option value="Loisirs" @if(request('category') == 'Loisirs') selected @endif>Loisirs</option>
                            <option value="Autre" @if(request('category') == 'Autre') selected @endif>Autre</option>
                        </select>
                    </div>

                    <!-- Filtres de prix -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="min_price" class="sr-only">Prix min</label>
                            <input type="number" id="min_price" name="min_price" placeholder="Prix min" class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ request('min_price') }}">
                        </div>
                        <div>
                            <label for="max_price" class="sr-only">Prix max</label>
                            <input type="number" id="max_price" name="max_price" placeholder="Prix max" class="w-full px-2 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-1">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            Filtrer
                        </button>
                        <a href="{{ route('welcome') }}" class="w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                            Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse ($items->where('status', 'available') as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <a href="{{ route('items.show', $item) }}">
                        @php
                            $imageUrl = $item->images->first() ? asset('storage/' . $item->images->first()->path) : asset('images/placeholder.jpg');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="{{ route('items.show', $item) }}">
                            <h2 class="font-bold text-lg mb-2">{{ $item->title }}</h2>
                        </a>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($item->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg">{{ $item->price }} €</span>
                            <a href="{{ route('items.show', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    <p>Aucun article trouvé. Essayez d'ajuster vos filtres de recherche.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-main-layout>
