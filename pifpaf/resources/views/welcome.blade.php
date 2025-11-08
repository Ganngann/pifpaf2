<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Trouvez la perle rare</h1>

        <!-- Formulaire de recherche et de filtrage -->
        <div class="mb-8 p-4 bg-gray-100 rounded-lg">
            <form action="{{ route('welcome') }}" method="GET" id="search-form">
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Champ de recherche par mot-clé -->
                    <div class="flex-grow min-w-[200px] md:flex-grow-[2]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                        <input type="text" id="search" name="search" placeholder="Que recherchez-vous ?" class="w-full px-4 py-2 border rounded-lg" value="{{ request('search') }}">
                    </div>

                    <!-- Filtre par catégorie -->
                    <div class="flex-grow min-w-[150px]">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                        <select id="category" name="category" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Toutes</option>
                            <option value="Vêtements" @if(request('category') == 'Vêtements') selected @endif>Vêtements</option>
                            <option value="Électronique" @if(request('category') == 'Électronique') selected @endif>Électronique</option>
                            <option value="Maison" @if(request('category') == 'Maison') selected @endif>Maison</option>
                            <option value="Sport" @if(request('category') == 'Sport') selected @endif>Sport</option>
                            <option value="Loisirs" @if(request('category') == 'Loisirs') selected @endif>Loisirs</option>
                            <option value="Autre" @if(request('category') == 'Autre') selected @endif>Autre</option>
                        </select>
                    </div>

                    <!-- Filtres de prix -->
                    <div class="flex-grow min-w-[180px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Min" class="w-full px-2 py-2 border rounded-lg" value="{{ request('min_price') }}">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Max" class="w-full px-2 py-2 border rounded-lg" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <!-- Filtres de distance -->
                    <div class="flex-grow min-w-[180px]">
                         <label class="block text-sm font-medium text-gray-700 mb-1">Localisation</label>
                         <div class="flex items-center gap-2">
                            <input type="text" name="location" placeholder="Autour de..." class="w-full px-4 py-2 border rounded-lg" value="{{ request('location') }}">
                            <select name="distance" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Dist.</option>
                                <option value="10" @if(request('distance') == '10') selected @endif>10 km</option>
                                <option value="25" @if(request('distance') == '25') selected @endif>25 km</option>
                                <option value="50" @if(request('distance') == '50') selected @endif>50 km</option>
                                <option value="100" @if(request('distance') == '100') selected @endif>100 km</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="flex-shrink-0">
                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <x-item-list :items="$items" />
    </div>
</x-main-layout>
