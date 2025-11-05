@props(['item'])

@if ($item->images->isNotEmpty())
    <div x-data="{ mainImageUrl: '{{ asset('storage/' . $item->primaryImage->path) }}' }">
        <!-- Image principale -->
        <div class="relative w-full h-96 bg-gray-200 flex items-center justify-center">
            <img :src="mainImageUrl" alt="{{ $item->title }}" class="w-full h-full object-contain {{ $item->status === \App\Enums\ItemStatus::SOLD ? 'opacity-40' : '' }}">
            @if ($item->status === \App\Enums\ItemStatus::SOLD)
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <span class="text-white text-5xl font-bold transform -rotate-12 border-4 border-white p-4 rounded-lg">VENDU</span>
                </div>
            @endif
        </div>

        <!-- Galerie de miniatures -->
        @if($item->images->count() > 1)
            <div class="flex space-x-2 p-4 bg-gray-100 overflow-x-auto">
                @foreach($item->images as $image)
                    @php
                        $imageUrl = asset('storage/' . $image->path);
                    @endphp
                    <img src="{{ $imageUrl }}"
                         alt="Miniature de {{ $item->title }}"
                         class="w-24 h-24 object-cover rounded-md cursor-pointer border-2 hover:border-blue-500"
                         :class="{ 'border-blue-500': mainImageUrl === '{{ $imageUrl }}' }"
                         @click="mainImageUrl = '{{ $imageUrl }}'">
                @endforeach
            </div>
        @endif
    </div>
@else
    <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
        <span class="text-gray-500">Aucune image disponible</span>
    </div>
@endif
