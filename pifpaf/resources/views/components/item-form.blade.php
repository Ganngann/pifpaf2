@props([
    'item' => null,
    'action',
    'method' => 'POST',
    'submitText'
])

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(strtoupper($method) !== 'POST')
        @method($method)
    @endif

    @if(session('image_path'))
        <input type="hidden" name="image_path" value="{{ session('image_path') }}">
    @endif

    <!-- Titre -->
    <div class="mb-4">
        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Titre</label>
        <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('title', $item->title ?? session('ai_data')['title'] ?? '') }}" required>
    </div>

    <!-- Description -->
    <div class="mb-4">
        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
        <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('description', $item->description ?? session('ai_data')['description'] ?? '') }}</textarea>
    </div>

    <!-- Catégorie -->
    <div class="mb-4">
        <label for="category" class="block text-gray-700 text-sm font-bold mb-2">Catégorie</label>
        <select name="category" id="category" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            <option value="">-- Choisir une catégorie --</option>
            @php
                $selectedCategory = old('category', $item->category ?? session('ai_data')['category'] ?? '');
                $categories = ['Vêtements', 'Électronique', 'Maison', 'Sport', 'Loisirs', 'Autre'];
            @endphp
            @foreach($categories as $category)
                <option value="{{ $category }}" @if($selectedCategory == $category) selected @endif>{{ $category }}</option>
            @endforeach
        </select>
    </div>

    <!-- Prix -->
    <div class="mb-4">
        <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Prix</label>
        <input type="number" step="0.01" name="price" id="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('price', $item->price ?? session('ai_data')['price'] ?? '') }}" required>
    </div>

    <!-- Slot pour les images -->
    {{ $images }}


    <div x-data="{ pickupAvailable: {{ old('pickup_available', $item->pickup_available ?? false) ? 'true' : 'false' }} }">
        <!-- Livraison à domicile -->
        <div class="mb-4">
            <label for="delivery_available" class="inline-flex items-center">
                <input type="checkbox" name="delivery_available" id="delivery_available" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" @if(old('delivery_available', $item->delivery_available ?? false)) checked @endif>
                <span class="ml-2 text-gray-700">Livraison à domicile disponible</span>
            </label>
        </div>

        <!-- Retrait sur place -->
        <div class="mb-4">
            <label for="pickup_available" class="inline-flex items-center">
                <input type="checkbox" name="pickup_available" id="pickup_available" x-model="pickupAvailable" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="1" @if(old('pickup_available', $item->pickup_available ?? false)) checked @endif>
                <span class="ml-2 text-gray-700">Retrait sur place disponible</span>
            </label>
        </div>

        <!-- Sélection de l'adresse de retrait (conditionnelle) -->
        <div x-show="pickupAvailable" class="mb-4">
            <label for="pickup_address_id" class="block text-gray-700 text-sm font-bold mb-2">Adresse de retrait</label>
            <select name="pickup_address_id" id="pickup_address_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">-- Choisir une adresse --</option>
                @foreach($pickupAddresses as $address)
                    <option value="{{ $address->id }}" @if(old('pickup_address_id', $item->pickup_address_id ?? null) == $address->id) selected @endif>
                        {{ $address->name }} - {{ $address->address }}, {{ $address->city }}, {{ $address->zip_code }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('profile.addresses.create') }}" class="text-sm text-blue-500 hover:text-blue-700 mt-1 inline-block">Ajouter une nouvelle adresse</a>
        </div>
    </div>

    <!-- Bouton de soumission -->
    <div class="flex items-center justify-end mt-4">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            {{ $submitText }}
        </button>
    </div>
</form>
