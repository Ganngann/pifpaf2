@props(['items'])

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse ($items as $item)
        <x-ui.item-card :item="$item" />
    @empty
        <x-ui.empty-state class="col-span-full">
            Aucun article trouvé.
            <x-slot name="actions">
                <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Retour à l'accueil
                </a>
            </x-slot>
        </x-ui.empty-state>
    @endforelse
</div>

@if ($items->hasPages())
    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
