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

                        <!-- Images -->
                        <div class="mb-4">
                            <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Images (jusqu'à 10)</label>

                            <!-- Affichage des images existantes -->
                            <div id="existing-images-container" class="mt-4 flex flex-wrap gap-4">
                                @foreach($item->images as $image)
                                    <div id="image-{{ $image->id }}" class="relative w-32 h-32">
                                        <img src="{{ asset('storage/' . $image->path) }}" class="w-full h-full object-cover rounded-md">
                                        <button type="button"
                                                onclick="deleteImage({{ $image->id }})"
                                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-4 mt-4 border border-dashed rounded-md">
                                <label for="images" class="block text-gray-700 text-sm font-bold mb-2">Ajouter de nouvelles images</label>
                                <input type="file" name="images[]" id="images" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100"
                                    multiple
                                    accept="image/png, image/jpeg">
                            </div>

                            <div id="image-preview-container" class="mt-4 flex flex-wrap gap-4">
                                <!-- Les prévisualisations des nouvelles images apparaîtront ici -->
                            </div>
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
    <script>
    function deleteImage(imageId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
            return;
        }

        const url = `/item-images/${imageId}`;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`image-${imageId}`).remove();
                // Mettre à jour le compteur d'images existantes pour la validation côté client
                window.existingImageCount--;
            } else {
                alert(data.message || 'Une erreur est survenue.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la suppression.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('images');
        const previewContainer = document.getElementById('image-preview-container');
        // Initialiser un compteur global
        window.existingImageCount = {{ $item->images->count() }};

        imageInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Vider les anciennes prévisualisations
            const files = event.target.files;

            if (files.length + window.existingImageCount > 10) {
                alert('Vous ne pouvez pas avoir plus de 10 images au total.');
                imageInput.value = ''; // Réinitialiser l'input
                return;
            }

            for (const file of files) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.classList.add('relative', 'w-32', 'h-32');

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
