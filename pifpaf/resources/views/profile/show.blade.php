<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden p-8">
            <h1 class="text-4xl font-bold mb-2">{{ $user->name }}</h1>

            {{-- Placeholder for ratings and reviews from US17 --}}
            <div class="mb-6">
                <span class="text-gray-500">Note moyenne :</span>
                <span class="font-semibold">N/A</span>
            </div>

            <div class="mt-8 pt-8 border-t">
                <h2 class="text-2xl font-bold mb-4">Articles en vente</h2>
                @if($user->items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                        @foreach($user->items as $item)
                            <div class="p-4 border rounded-lg shadow-sm">
                                <a href="{{ route('items.show', $item) }}">
                                    @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover rounded-md">
                                    @endif
                                    <h5 class="text-lg font-bold mt-2">{{ $item->title }}</h5>
                                </a>
                                <p class="text-gray-600">{{ $item->price }} €</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4">Cet utilisateur n'a aucun article en vente pour le moment.</p>
                @endif
            </div>
        </div>
    </div>
</x-main-layout>
