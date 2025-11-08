@props(['item'])

<div class="border rounded-lg shadow-lg overflow-hidden">
    {{-- Main content of the card, linking to the item edit page --}}
    <a href="{{ route('items.edit', $item) }}" class="block p-4 hover:bg-gray-50 transition-colors">
        <div class="flex items-center">
            {{-- Thumbnail --}}
            <div class="flex-shrink-0 h-16 w-16">
                <x-ui.item-thumbnail :item="$item" class="h-16 w-16 object-cover rounded" />
            </div>
            {{-- Item Info --}}
            <div class="ml-4 flex-grow">
                <h4 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h4>
                <p class="text-sm font-bold text-gray-700 mt-1">{{ number_format($item->price, 2, ',', ' ') }} €</p>
                <x-ui.status-badge :status="$item->status" class="mt-2" />
            </div>
        </div>
    </a>

    {{-- Actions Slot --}}
    @if(isset($actions))
        <div {{ $actions->attributes->class(['p-4 border-t flex flex-wrap justify-end gap-2 bg-gray-50']) }}>
            {{ $actions }}
        </div>
    @endif

    {{-- Offers Slot --}}
    @if(isset($offers))
        <div {{ $offers->attributes->class(['border-t bg-gray-50']) }}>
            {{ $offers }}
        </div>
    @endif
</div>
