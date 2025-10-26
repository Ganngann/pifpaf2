<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ session('ai_data') ? 'Valider les informations de l\'annonce' : 'Créer une nouvelle annonce' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Affichage des erreurs de validation -->
                    @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        @if(session('image_path'))
                            <input type="hidden" name="image_path" value="{{ session('image_path') }}">
                        @endif

                        <!-- Titre -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('title', session('ai_data')['title'] ?? '') }}" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('description', session('ai_data')['description'] ?? '') }}</textarea>
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-4">
                            <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                            <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">-- Choisir une catégorie --</option>
                                @php
                                    $selectedCategory = old('category', session('ai_data')['category'] ?? '');
                                @endphp
                                <option value="Vêtements" @if($selectedCategory == 'Vêtements') selected @endif>Vêtements</option>
                                <option value="Électronique" @if($selectedCategory == 'Électronique') selected @endif>Électronique</option>
                                <option value="Maison" @if($selectedCategory == 'Maison') selected @endif>Maison</option>
                                <option value="Sport" @if($selectedCategory == 'Sport') selected @endif>Sport</option>
                                <option value="Loisirs" @if($selectedCategory == 'Loisirs') selected @endif>Loisirs</option>
                                <option value="Autre" @if($selectedCategory == 'Autre') selected @endif>Autre</option>
                            </select>
                        </div>

                        <!-- Prix -->
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix</label>
                            <input type="number" step="0.01" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('price', session('ai_data')['price'] ?? '') }}" required>
                        </div>

                        <!-- Image -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                            @if(session('image_path'))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . session('image_path')) }}" alt="Image de l'article" class="w-48 h-auto rounded">
                                    <p class="text-sm text-gray-600 mt-1">Image actuelle. Vous pouvez en choisir une autre pour la remplacer.</p>
                                </div>
                            @endif
                            <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" {{ session('image_path') ? '' : 'required' }}>
                        </div>


                        <!-- Bouton de soumission -->
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ session('ai_data') ? 'Mettre en vente' : 'Créer l\'annonce' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
