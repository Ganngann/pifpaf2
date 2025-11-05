@props(['items'])

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse ($items as $item)
        <x-ui.item-card :item="$item" />
    @empty
        <div class="col-span-full text-center text-gray-500">
            <p>Aucun article trouvé.</p>
        </div>
    @endforelse
</div>

@if ($items->hasPages())
    <div class="mt-8">
        {{ $items->links() }}
    </div>
@endif
