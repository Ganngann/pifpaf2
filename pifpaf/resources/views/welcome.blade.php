<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Derniers articles</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach ($items as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <a href="{{ route('items.show', $item) }}">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                    </a>
                    <div class="p-4">
                        <a href="{{ route('items.show', $item) }}">
                            <h2 class="font-bold text-lg mb-2">{{ $item->title }}</h2>
                        </a>
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($item->description, 100) }}</p>
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-lg">{{ $item->price }} €</span>
                            <a href="{{ route('items.show', $item) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-main-layout>
