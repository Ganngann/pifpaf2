<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-96 object-cover">
            <div class="p-8">
                <h1 class="text-4xl font-bold mb-4">{{ $item->title }}</h1>
                <p class="text-gray-600 mb-8">{{ $item->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="font-bold text-3xl">{{ $item->price }} €</span>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded">
                        Acheter
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
