<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
    <input type="text" name="name" id="name" value="{{ old('name', $address->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
</div>

<div class="mb-4">
    <label for="street" class="block text-sm font-medium text-gray-700">Rue</label>
    <input type="text" name="street" id="street" value="{{ old('street', $address->street ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="city" class="block text-sm font-medium text-gray-700">Ville</label>
        <input type="text" name="city" id="city" value="{{ old('city', $address->city ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>
    <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700">Code Postal</label>
        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
    </div>
</div>

@php
$countries = [
    'FR' => 'France',
    'BE' => 'Belgique',
    'LU' => 'Luxembourg',
    'DE' => 'Allemagne',
    'NL' => 'Pays-Bas',
    'ES' => 'Espagne',
    'IT' => 'Italie',
    'CH' => 'Suisse',
];
@endphp
<div class="mb-4">
    <label for="country" class="block text-sm font-medium text-gray-700">Pays</label>
    <select name="country" id="country" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="">Sélectionnez un pays</option>
        @foreach($countries as $code => $name)
            <option value="{{ $code }}" {{ old('country', $address->country ?? '') == $code ? 'selected' : '' }}>
                {{ $name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <span class="block text-sm font-medium text-gray-700">Type d'adresse</span>
    @if ($errors->has('type'))
        <p class="text-sm text-red-600 mt-2">{{ $errors->first('type') }}</p>
    @endif
    <div class="mt-2 space-y-2">
        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_for_pickup" value="1" {{ old('is_for_pickup', $address->is_for_pickup ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2">Adresse de retrait</span>
            </label>
        </div>
        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_for_delivery" value="1" {{ old('is_for_delivery', $address->is_for_delivery ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-offset-0 focus:ring-indigo-200 focus:ring-opacity-50">
                <span class="ml-2">Adresse de livraison</span>
            </label>
        </div>
    </div>
</div>
