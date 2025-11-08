<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes Analyses IA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('success')" />
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <div class="mb-4">
                        <a href="{{ route('items.create-with-ai') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Lancer une nouvelle analyse') }}
                        </a>
                    </div>

                    @forelse ($requests as $request)
                        <div class="mb-6 p-4 border rounded-lg shadow-sm" x-data="{ open: false }" dusk="ai-request-{{ $request->id }}">
                            <div class="flex justify-between items-center cursor-pointer" @click="open = !open" dusk="ai-request-header-{{ $request->id }}">
                                <div class="flex items-center">
                                    <img src="{{ asset('storage/' . $request->image_path) }}" alt="Image analysée" class="w-16 h-16 object-cover rounded-lg mr-4">
                                    <div>
                                        <p class="font-bold text-lg">Demande du {{ $request->created_at->format('d/m/Y à H:i') }}</p>
                                        <p class="text-sm text-gray-600">Statut : <span class="font-semibold">{{ $request->status }}</span></p>
                                        @if ($request->status === 'completed')
                                            <p class="text-sm text-gray-600">
                                                {{ count($request->created_item_ids ?? []) }} / {{ count($request->result) }} objets créés
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div x-text="open ? '-' : '+'" class="text-2xl font-bold"></div>
                            </div>

                            <div x-show="open" class="mt-4">
                                @if ($request->status === 'completed')
                                    <div x-data="selectObject_{{ $request->id }}()">
                                        <div class="lg:flex lg:space-x-8">
                                            <div class="lg:w-2/3">
                                                <div class="relative" id="image-container-{{ $request->id }}">
                                                    <img id="main-image-{{ $request->id }}" src="{{ asset('storage/' . $request->image_path) }}" alt="Image à analyser" class="max-w-full h-auto rounded-lg">
                                                    <template x-if="selectedBox">
                                                        <div class="absolute border-4 border-blue-500 pointer-events-none" :style="`top: ${selectedBox.y}%; left: ${selectedBox.x}%; width: ${selectedBox.w}%; height: ${selectedBox.h}%;`"></div>
                                                    </template>
                                                </div>
                                            </div>
                                            <div class="mt-4 lg:mt-0 lg:w-1/3">
                                                <h4 class="font-bold">Objets détectés :</h4>
                                                <ul class="mt-4 space-y-2">
                                                    <template x-for="(item, index) in items" :key="index">
                                                        <li class="p-2 rounded-lg cursor-pointer border"
                                                            :class="{ 'bg-gray-200 border-blue-500': selectedBox && selectedBox.index === index, 'bg-white': !selectedBox || selectedBox.index !== index }"
                                                            @click="selectItem(index)">
                                                            <div class="flex items-center space-x-3">
                                                                <img :src="getCropUrl(item.box)" alt="Aperçu" class="w-16 h-16 object-cover rounded-md bg-gray-100">
                                                                <div class="flex-grow">
                                                                    <span x-text="item.title"></span>
                                                                </div>
                                                                <div class="flex-shrink-0">
                                                                    <template x-if="!item.created">
                                                                        <button @click.stop="createItem(index)"
                                                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                                            Créer
                                                                        </button>
                                                                    </template>
                                                                    <template x-if="item.created">
                                                                        <a :href="item.item_url"
                                                                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">
                                                                            Voir
                                                                        </a>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        function selectObject_{{ $request->id }}() {
                                            return {
                                                boxes: [],
                                                items: [],
                                                selectedBox: null,

                                                init() {
                                                    const createdItems = @json($request->created_item_ids ?? (object)[]);
                                                    this.items = (@json($request->result) || []).map((item, index) => ({
                                                        ...item,
                                                        created: createdItems.hasOwnProperty(index),
                                                        item_url: createdItems.hasOwnProperty(index) ? `/items/${createdItems[index]}` : null
                                                    }));

                                                    const image = document.getElementById('main-image-{{ $request->id }}');
                                                    const setup = () => {
                                                        this.calculateBoxes();
                                                        // Par défaut, on sélectionne le premier objet non créé
                                                        const firstUncreated = this.items.findIndex(item => !item.created);
                                                        if (firstUncreated !== -1) {
                                                            this.selectItem(firstUncreated);
                                                        } else if (this.items.length > 0) {
                                                            this.selectItem(0); // Fallback au premier si tous sont créés
                                                        }
                                                    };

                                                    if (image.complete) {
                                                        setup();
                                                    } else {
                                                        image.onload = setup;
                                                    }
                                                },

                                                getCropUrl(box) {
                                                    if (!box) return '';
                                                    const params = new URLSearchParams({
                                                        image_path: '{{ $request->image_path }}',
                                                        box: JSON.stringify(box)
                                                    });
                                                    return `{{ route('ai.requests.crop_preview') }}?${params.toString()}`;
                                                },

                                                calculateBoxes() {
                                                    const image = document.getElementById('main-image-{{ $request->id }}');
                                                    if (!image || !image.clientWidth) return;

                                                    const renderedWidth = image.clientWidth;
                                                    const renderedHeight = image.clientHeight;
                                                    const naturalWidth = image.naturalWidth;
                                                    const naturalHeight = image.naturalHeight;
                                                    const imageAspectRatio = naturalWidth / naturalHeight;
                                                    const renderedAspectRatio = renderedWidth / renderedHeight;

                                                    let offsetX = 0,
                                                        offsetY = 0,
                                                        effectiveWidth = renderedWidth,
                                                        effectiveHeight = renderedHeight;

                                                    if (Math.abs(imageAspectRatio - renderedAspectRatio) > 0.01) {
                                                        if (imageAspectRatio > renderedAspectRatio) {
                                                            effectiveHeight = renderedWidth / imageAspectRatio;
                                                            offsetY = (renderedHeight - effectiveHeight) / 2;
                                                        } else {
                                                            effectiveWidth = renderedHeight * imageAspectRatio;
                                                            offsetX = (renderedWidth - effectiveWidth) / 2;
                                                        }
                                                    }

                                                    this.boxes = this.items.filter(item => item.box).map((item, index) => {
                                                        const box = item.box;
                                                        const marginPct = 5;

                                                        const x1_pct = Math.max(0, (box.x1 / 10) - marginPct);
                                                        const y1_pct = Math.max(0, (box.y1 / 10) - marginPct);
                                                        const w_pct = Math.min(100 - x1_pct, ((box.x2 - box.x1) / 10) + 2 * marginPct);
                                                        const h_pct = Math.min(100 - y1_pct, ((box.y2 - box.y1) / 10) + 2 * marginPct);

                                                        const x_px = (x1_pct / 100 * effectiveWidth) + offsetX;
                                                        const y_px = (y1_pct / 100 * effectiveHeight) + offsetY;
                                                        const w_px = (w_pct / 100 * effectiveWidth);
                                                        const h_px = (h_pct / 100 * effectiveHeight);

                                                        return {
                                                            x: (x_px / renderedWidth) * 100,
                                                            y: (y_px / renderedHeight) * 100,
                                                            w: (w_px / renderedWidth) * 100,
                                                            h: (h_px / renderedHeight) * 100,
                                                            index: index
                                                        };
                                                    });
                                                },

                                                selectItem(index) {
                                                    this.selectedBox = this.boxes.find(box => box.index === index);
                                                },

                                                createItem(index) {
                                                    const itemData = this.items[index];
                                                    const originalImagePath = '{{ $request->image_path }}';

                                                    fetch('{{ route('items.create-from-ai') }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/json',
                                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                            },
                                                            body: JSON.stringify({
                                                                item_data: JSON.stringify(itemData),
                                                                original_image_path: originalImagePath,
                                                                item_index: index
                                                            })
                                                        })
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            if (data.success) {
                                                                this.items[index].created = true;
                                                                this.items[index].item_url = data.item_url;
                                                            } else {
                                                                alert(data.message || 'Une erreur est survenue.');
                                                            }
                                                        });
                                                }
                                            }
                                        }
                                    </script>
                                @elseif($request->status === 'failed')
                                    <div x-data="{ showDetails: false }">
                                        <div class="flex items-center justify-between">
                                            <p class="text-red-500">{{ $request->error_message }}</p>
                                            <div class="flex items-center space-x-2">
                                                @if ($request->raw_error_response)
                                                    <button @click="showDetails = !showDetails" class="text-sm text-blue-500 hover:underline">
                                                        <span x-show="!showDetails">Voir les détails</span>
                                                        <span x-show="showDetails">Cacher les détails</span>
                                                    </button>
                                                @endif
                                                @if ($request->retry_count < 3)
                                                    <form action="{{ route('ai-requests.retry', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                                            Relancer l'analyse ({{ $request->retry_count }}/3)
                                                        </button>
                                                    </form>
                                                @else
                                                    <p class="text-sm text-gray-500">Nombre maximum de tentatives atteint.</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div x-show="showDetails" class="mt-4 p-4 bg-gray-100 rounded">
                                            <h4 class="font-bold">Détails de l'erreur :</h4>
                                            <pre class="whitespace-pre-wrap text-sm text-gray-700">{{ $request->raw_error_response }}</pre>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-gray-500">Analyse en cours...</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p>Vous n'avez aucune analyse IA pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
