@props(['item'])

<div class="bg-gray-50 p-4 rounded-lg border">
    <h2 class="text-lg font-semibold mb-3">Modes de livraison</h2>
    <div class="space-y-3">
        @if ($item->pickup_available && $item->pickupAddress)
            <label class="flex items-center p-3 border rounded-md has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400 cursor-pointer transition-all duration-200 ease-in-out">
                <input type="radio" name="delivery_method_choice" value="pickup" x-model="deliveryMethod" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div class="ml-3 text-sm">
                    <p class="font-medium text-gray-900">Retrait sur place</p>
                    <p class="text-gray-500">À {{ $item->pickupAddress->city }}</p>
                </div>
            </label>
        @endif

        @if ($item->delivery_available)
            <label class="flex items-center p-3 border rounded-md has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400 cursor-pointer transition-all duration-200 ease-in-out">
                <input type="radio" name="delivery_method_choice" value="delivery" x-model="deliveryMethod" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div class="ml-3 text-sm">
                    <p class="font-medium text-gray-900">Livraison</p>
                    <p class="text-gray-500">Envoi par colis postal</p>
                </div>
            </label>
        @endif
    </div>
</div>
