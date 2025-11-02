<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
        <input type="text" name="name" id="name" value="{{ old('name', $address->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>

    <div>
        <label for="street" class="block text-sm font-medium text-gray-700">Rue</label>
        <input type="text" name="street" id="street" value="{{ old('street', $address->street ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>

    <div>
        <label for="city" class="block text-sm font-medium text-gray-700">Ville</label>
        <input type="text" name="city" id="city" value="{{ old('city', $address->city ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>

    <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700">Code Postal</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>
</div>

<div class="mt-6 flex justify-end">
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Enregistrer
    </button>
</div>
