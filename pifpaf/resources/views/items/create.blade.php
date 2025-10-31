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

                        <!-- Images -->
                        <div class="mb-4">
                            <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Images (jusqu'à 10)</label>

                            <div class="p-4 border border-dashed rounded-md">
                                <input type="file" name="images[]" id="images" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100"
                                    multiple
                                    accept="image/png, image/jpeg"
                                    {{ session('image_path') ? '' : 'required' }}>
                            </div>

                            <div id="image-preview-container" class="mt-4 flex flex-wrap gap-4">
                                @if(session('image_path'))
                                    <div class="relative w-32 h-32">
                                        <img src="{{ asset('storage/' . session('image_path')) }}" class="w-full h-full object-cover rounded-md">
                                        <p class="text-xs text-center mt-1">Image de l'IA</p>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <!-- Retrait sur place -->
                        <div class="mb-4">
                            <label for="pickup_available" class="inline-flex items-center">
                                <input type="checkbox" name="pickup_available" id="pickup_available" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" {{ old('pickup_available') ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Retrait sur place disponible</span>
                            </label>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        const initialImageBlock = previewContainer.querySelector('.initial-image');

        imageInput.addEventListener('change', function(event) {
            // Vider le conteneur des anciennes prévisualisations (sauf l'image de l'IA)
            previewContainer.querySelectorAll('.preview-wrapper').forEach(el => el.remove());

            const files = event.target.files;
            const currentTotal = (initialImageBlock ? 1 : 0);

            if (files.length + currentTotal > 10) {
                alert('Vous ne pouvez pas télécharger plus de 10 images au total.');
                // Réinitialiser l'input
                imageInput.value = '';
                return;
            }

            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewWrapper = document.createElement('div');
                    // On ajoute une classe spécifique pour les différencier de l'image IA
                    previewWrapper.classList.add('relative', 'w-32', 'h-32', 'preview-wrapper');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('w-full', 'h-full', 'object-cover', 'rounded-md');

                    previewWrapper.appendChild(img);
                    previewContainer.appendChild(previewWrapper);
                }
                reader.readAsDataURL(file);
            }
        });
    });
    </script>
</x-app-layout>
