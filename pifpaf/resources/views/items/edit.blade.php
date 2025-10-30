<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'annonce') }}
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

                    <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Titre -->
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('title', $item->title) }}" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('description', $item->description) }}</textarea>
                        </div>

                        <!-- Catégorie -->
                        <div class="mb-4">
                            <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
                            <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">-- Choisir une catégorie --</option>
                                <option value="Vêtements" @if(old('category', $item->category) == 'Vêtements') selected @endif>Vêtements</option>
                                <option value="Électronique" @if(old('category', $item->category) == 'Électronique') selected @endif>Électronique</option>
                                <option value="Maison" @if(old('category', $item->category) == 'Maison') selected @endif>Maison</option>
                                <option value="Sport" @if(old('category', $item->category) == 'Sport') selected @endif>Sport</option>
                                <option value="Loisirs" @if(old('category', $item->category) == 'Loisirs') selected @endif>Loisirs</option>
                                <option value="Autre" @if(old('category', $item->category) == 'Autre') selected @endif>Autre</option>
                            </select>
                        </div>

                        <!-- Prix -->
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix</label>
                            <input type="number" step="0.01" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('price', $item->price) }}" required>
                        </div>

                        <!-- Image actuelle -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Image actuelle</label>
                            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-1/4 h-auto rounded">
                        </div>

                        <!-- Nouvelle image -->
                        <div class="mb-4">
                            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Changer l'image</label>
                            <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <!-- Retrait sur place -->
                        <div class="mb-4">
                            <label for="pickup_available" class="inline-flex items-center">
                                <input type="checkbox" name="pickup_available" id="pickup_available" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" @if(old('pickup_available', $item->pickup_available)) checked @endif>
                                <span class="ml-2 text-gray-700">Retrait sur place disponible</span>
                            </label>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
