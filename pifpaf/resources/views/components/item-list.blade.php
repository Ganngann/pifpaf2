@props(['items'])

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse ($items as $item)
        <x-ui.item-card :item="$item" />
    @empty
        <div class="col-span-full">
            <x-ui.empty-state>
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </x-slot>
                Aucun article trouvé.
                <x-slot name="description">
                    Nous n'avons trouvé aucun article correspondant à vos critères pour le moment.
                </x-slot>
                <x-slot name="actions">
                    <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Vendre un article
                    </a>
                </x-slot>
            </x-ui.empty-state>
        </div>
    @endforelse
</div>

@if ($items->hasPages())
    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
