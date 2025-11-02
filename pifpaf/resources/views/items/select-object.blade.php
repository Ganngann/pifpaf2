<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sélectionnez un objet à vendre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <p class="mb-4 text-gray-600">
                        Nous avons détecté plusieurs objets sur votre photo. Cliquez sur l'objet que vous souhaitez mettre en vente.
                    </p>

                    @php
                        // Assurer que $items_data est toujours un tableau d'objets pour la boucle
                        $items = isset($items_data[0]) && is_array($items_data[0]) ? $items_data : [$items_data];
                    @endphp


                    <div x-data="selectObject()" @load.window="init()">
                        <div class="relative inline-block" id="image-container">
                            <img id="main-image" src="{{ asset('storage/' . $image_path) }}" alt="Image à analyser" class="max-w-full h-auto rounded-lg">

                            @foreach ($items as $index => $item)
                                @if(isset($item['box']))
                                <div
                                    class="absolute border-4 border-blue-500 hover:bg-blue-500 hover:bg-opacity-25 cursor-pointer"
                                    :style="`top: ${boxes[{{ $index }}].y}%; left: ${boxes[{{ $index }}].x}%; width: ${boxes[{{ $index }}].w}%; height: ${boxes[{{ $index }}].h}%;`"
                                    @click="selectItem({{ $index }})">
                                    <span class="absolute -top-7 left-0 bg-blue-500 text-white text-sm font-bold p-1 rounded">
                                        {{ $item['title'] }}
                                    </span>
                                </div>
                                @endif
                            @endforeach
                        </div>

                        <form x-ref="form" action="{{ route('items.create-from-selection') }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="original_image_path" value="{{ $image_path }}">
                            <input type="hidden" name="item_data" x-ref="itemData">
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function selectObject() {
            return {
                boxes: [],
                items: @json($items),

                init() {
                    const image = document.getElementById('main-image');
                    // On attend que l'image soit chargée pour avoir ses dimensions
                    image.onload = () => {
                        this.calculateBoxes();
                    };
                    // Si l'image est déjà chargée (cache)
                    if (image.complete) {
                        this.calculateBoxes();
                    }
                },

                calculateBoxes() {
                    this.boxes = this.items.filter(item => item.box).map(item => {
                        const box = item.box;
                        // The coordinates from Gemini API are normalized (0.0 to 1.0).
                        // We multiply by 100 to get percentages for CSS positioning.
                        return {
                            x: box.x1 * 100,
                            y: box.y1 * 100,
                            w: (box.x2 - box.x1) * 100,
                            h: (box.y2 - box.y1) * 100,
                        };
                    });
                },

                selectItem(index) {
                    this.$refs.itemData.value = JSON.stringify(this.items[index]);
                    this.$refs.form.submit();
                }
            }
        }
    </script>
</x-app-layout>
