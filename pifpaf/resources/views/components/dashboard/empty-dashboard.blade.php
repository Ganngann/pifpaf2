<div class="text-center text-gray-500">
    <x-ui.empty-state>
        Vous n'avez pas encore d'annonce.
        <x-slot name="actions">
            <a href="{{ route('items.create') }}" class="mt-2 inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">
                Créer ma première annonce
            </a>
        </x-slot>
    </x-ui.empty-state>
</div>
