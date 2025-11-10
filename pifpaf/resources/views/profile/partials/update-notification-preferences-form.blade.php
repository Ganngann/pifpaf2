<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Préférences de Notification') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Choisissez les notifications que vous souhaitez recevoir.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @php
            $preferences = auth()->user()->notification_preferences ?? [];
        @endphp

        <div>
            <label for="new_offer" class="inline-flex items-center">
                <input id="new_offer" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[new_offer]" {{ $preferences['new_offer'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Nouvelle offre sur un de mes articles') }}</span>
            </label>
        </div>

        <div>
            <label for="offer_accepted" class="inline-flex items-center">
                <input id="offer_accepted" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[offer_accepted]" {{ $preferences['offer_accepted'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Mon offre a été acceptée') }}</span>
            </label>
        </div>

        <div>
            <label for="offer_rejected" class="inline-flex items-center">
                <input id="offer_rejected" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[offer_rejected]" {{ $preferences['offer_rejected'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Mon offre a été refusée') }}</span>
            </label>
        </div>

        <div>
            <label for="payment_received" class="inline-flex items-center">
                <input id="payment_received" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[payment_received]" {{ $preferences['payment_received'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Paiement reçu pour une de mes ventes') }}</span>
            </label>
        </div>

        <div>
            <label for="shipment" class="inline-flex items-center">
                <input id="shipment" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[shipment]" {{ $preferences['shipment'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __("Un article que j'ai acheté a été envoyé") }}</span>
            </label>
        </div>

        <div>
            <label for="reception_confirmed" class="inline-flex items-center">
                <input id="reception_confirmed" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[reception_confirmed]" {{ $preferences['reception_confirmed'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __("L'acheteur a confirmé la réception d'un de mes articles") }}</span>
            </label>
        </div>

        <div>
            <label for="new_message" class="inline-flex items-center">
                <input id="new_message" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[new_message]" {{ $preferences['new_message'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Nouveau message dans une de mes conversations') }}</span>
            </label>
        </div>

        <div>
            <label for="confirmation_reminder" class="inline-flex items-center">
                <input id="confirmation_reminder" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="notification_preferences[confirmation_reminder]" {{ $preferences['confirmation_reminder'] ?? true ? 'checked' : '' }}>
                <span class="ml-2 text-sm text-gray-600">{{ __('Rappel pour confirmer la réception d\'un article') }}</span>
            </label>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
