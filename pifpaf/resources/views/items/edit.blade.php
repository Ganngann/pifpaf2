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

                    <x-item-form
                        :item="$item"
                        :action="route('items.update', $item)"
                        method="PUT"
                        submit-text="Mettre à jour"
                        :pickup-addresses="$pickupAddresses">

                        <x-slot name="images">
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Images (jusqu'à 10)</label>

                                <!-- Affichage des images existantes -->
                                <div id="existing-images-container" class="mt-4 flex flex-wrap gap-4">
                                    @foreach($item->images as $image)
                                        <div id="image-{{ $image->id }}" data-id="{{ $image->id }}" class="relative w-32 h-32 group cursor-move">
                                            <img src="{{ asset('storage/' . $image->path) }}" class="w-full h-full object-cover rounded-md @if($image->is_primary) border-4 border-blue-500 @endif">
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                @if(!$image->is_primary)
                                                    <button type="button"
                                                            onclick="setAsPrimary('{{ route('item-images.set-primary', $image->id) }}')"
                                                            class="text-white text-xs bg-blue-500 hover:bg-blue-700 rounded-full px-2 py-1 mb-1">
                                                        Principale
                                                    </button>
                                                @endif
                                                <button type="button"
                                                        onclick="deleteImage('{{ route('item-images.destroy', $image->id) }}')"
                                                        class="text-white text-xs bg-red-500 hover:bg-red-700 rounded-full px-2 py-1">
                                                    Supprimer
                                                </button>
                                            </div>
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
                        </x-slot>
                    </x-item-form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
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

        const el = document.getElementById('existing-images-container');
        const sortable = Sortable.create(el, {
            animation: 150,
            onEnd: function () {
                const order = sortable.toArray();
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch('{{ route('item-images.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ids: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('La réorganisation a échoué.');
                    }
                });
            },
        });
    });

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    function setAsPrimary(url) {
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                alert('Une erreur est survenue.');
            }
        });
    }

    function deleteImage(url) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Une erreur est survenue.');
                }
            });
        }
    }
    </script>
</x-app-layout>
