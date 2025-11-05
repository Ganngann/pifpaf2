@props(['item'])

@if ($item->images->isNotEmpty())
    <div x-data="{ mainImageUrl: '{{ asset('storage/' . $item->primaryImage->path) }}' }">
        <!-- Image principale -->
        <div class="relative w-full h-[500px] bg-gray-100 flex items-center justify-center p-4">
            <img :src="mainImageUrl" alt="{{ $item->title }}" class="max-w-full max-h-full object-contain {{ $item->status === \App\Enums\ItemStatus::SOLD ? 'opacity-40' : '' }}">
            @if ($item->status === \App\Enums\ItemStatus::SOLD)
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                    <span class="text-white text-5xl font-bold transform -rotate-12 border-4 border-white p-4 rounded-lg">VENDU</span>
                </div>
            @endif
        </div>

        <!-- Galerie de miniatures -->
        @if($item->images->count() > 1)
            <div class="flex justify-center space-x-4 p-4 bg-white border-t">
                @foreach($item->images as $image)
                    @php
                        $imageUrl = asset('storage/' . $image->path);
                    @endphp
                    <button @click="mainImageUrl = '{{ $imageUrl }}'"
                            class="w-24 h-24 rounded-md overflow-hidden border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            :class="{ 'border-blue-500': mainImageUrl === '{{ $imageUrl }}', 'border-transparent': mainImageUrl !== '{{ $imageUrl }}' }">
                        <img src="{{ $imageUrl }}"
                             alt="Miniature de {{ $item->title }}"
                             class="w-full h-full object-cover">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
@else
    <div class="w-full h-[500px] bg-gray-100 flex items-center justify-center">
        <span class="text-gray-500">Aucune image disponible</span>
    </div>
@endif
