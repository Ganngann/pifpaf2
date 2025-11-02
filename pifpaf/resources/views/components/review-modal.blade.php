@props(['transaction', 'recipientName'])

<div x-data="{ open: false }" class="inline-block">
    <!-- Le bouton pour ouvrir la modale -->
    <button @click="open = true" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 text-sm">
        Laisser un avis
    </button>

    <!-- La modale -->
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         x-cloak>
        <div @click.away="open = false" class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full">
            <h3 class="text-2xl font-bold mb-4">Évaluer {{ $recipientName }}</h3>
            <form action="{{ route('reviews.store', $transaction) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rating-{{ $transaction->id }}" class="block text-sm font-medium text-gray-700">Note (sur 5)</label>
                    <input type="number" name="rating" id="rating-{{ $transaction->id }}" min="1" max="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mb-6">
                    <label for="comment-{{ $transaction->id }}" class="block text-sm font-medium text-gray-700">Commentaire (facultatif)</label>
                    <textarea name="comment" id="comment-{{ $transaction->id }}" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Partagez votre expérience..."></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" @click="open = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
