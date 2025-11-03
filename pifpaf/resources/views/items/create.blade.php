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

                    <!-- Bouton pour la création assistée par IA -->
                    <div class="mb-6 text-center">
                        <a href="{{ route('items.create-with-ai') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition-transform transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            🚀 Essayer la création d'annonce par IA
                        </a>
                        <p class="text-sm text-gray-500 mt-2">Gagnez du temps en laissant notre IA remplir les champs pour vous !</p>
                    </div>

                    <div class="my-4 border-t border-gray-200"></div>

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

                    <x-item-form
                        :action="route('items.store')"
                        :submit-text="session('ai_data') ? 'Mettre en vente' : 'Créer l\'annonce'"
                        :pickup-addresses="$pickupAddresses">

                        <x-slot name="images">
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
                        </x-slot>
                    </x-item-form>
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
