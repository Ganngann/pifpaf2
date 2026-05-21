@props(['items'])

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse ($items as $item)
        <x-ui.item-card :item="$item" />
    @empty
        <div class="col-span-full">
            <x-ui.empty-state>Aucun article trouvé.</x-ui.empty-state>
        </div>
    @endforelse
</div>

@if ($items->hasPages())
    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
