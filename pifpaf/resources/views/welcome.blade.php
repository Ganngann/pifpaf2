<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Trouvez la perle rare</h1>

        <!-- Formulaire de recherche et de filtrage -->
        <div class="mb-8 p-4 bg-gray-100 rounded-lg">
            <form action="{{ route('welcome') }}" method="GET" id="search-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Champ de recherche par mot-clé -->
                <div class="md:col-span-2">
                    <input type="text" name="search" placeholder="Que recherchez-vous ?" class="w-full px-4 py-2 border rounded-lg" value="{{ request('search') }}">
                </div>

                <!-- Filtre par catégorie -->
                <div>
                    <select name="category" class="w-full px-4 py-2 border rounded-lg">
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
                    <input type="number" name="min_price" placeholder="Prix min" class="w-full px-2 py-2 border rounded-lg" value="{{ request('min_price') }}">
                    <input type="number" name="max_price" placeholder="Prix max" class="w-full px-2 py-2 border rounded-lg" value="{{ request('max_price') }}">
                </div>

                <!-- Nouveaux filtres de distance -->
                <div class="grid grid-cols-1 gap-2">
                    <input type="text" name="location" placeholder="Autour de... (ex: Paris)" class="w-full px-4 py-2 border rounded-lg" value="{{ request('location') }}">
                    <select name="distance" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Distance</option>
                        <option value="10" @if(request('distance') == '10') selected @endif>10 km</option>
                        <option value="25" @if(request('distance') == '25') selected @endif>25 km</option>
                        <option value="50" @if(request('distance') == '50') selected @endif>50 km</option>
                        <option value="100" @if(request('distance') == '100') selected @endif>100 km</option>
                    </select>
                </div>
            </form>
            <!-- Bouton de soumission sur une nouvelle ligne pour un meilleur affichage -->
            <div class="mt-4 text-center">
                <button type="submit" form="search-form" class="w-full md:w-auto bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    Rechercher
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @forelse ($items as $item)
                <x-item-card :item="$item" />
            @empty
                <div class="col-span-full text-center text-gray-500">
                    <p>Aucun article trouvé. Essayez d'ajuster vos filtres de recherche.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-main-layout>
