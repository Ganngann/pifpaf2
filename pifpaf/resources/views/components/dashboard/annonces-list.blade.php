@props(['items'])

<h3 class="text-2xl font-bold mb-6 text-center sm:text-left">Mes annonces</h3>

<!-- Vue Tableau pour Desktop -->
<div class="hidden sm:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Annonce
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Prix
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($items as $item)
                <tr id="item-row-{{ $item->id }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if ($item->primaryImage && $item->primaryImage->path)
                                    <img class="h-10 w-10 object-cover" src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}">
                                @else
                                    <div class="h-10 w-10 bg-gray-200 flex items-center justify-center">
                                        <span class="text-xs text-gray-500">?</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('items.edit', $item) }}" class="hover:text-blue-600 transition-colors">{{ $item->title }}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                         <span id="status-badge-{{ $item->id }}" @class([
                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                            'bg-green-100 text-green-800' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
                            'bg-gray-100 text-gray-800' => $item->status === \App\Enums\ItemStatus::UNPUBLISHED,
                            'bg-blue-100 text-blue-800' => $item->status === \App\Enums\ItemStatus::SOLD,
                        ])>
                            @if($item->status === \App\Enums\ItemStatus::AVAILABLE)
                                En ligne
                            @elseif($item->status === \App\Enums\ItemStatus::UNPUBLISHED)
                                Hors ligne
                            @else
                                Vendu
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ number_format($item->price, 2, ',', ' ') }} €
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2" id="actions-{{ $item->id }}">
                            @if ($item->status !== \App\Enums\ItemStatus::SOLD)
                                <button
                                    data-item-id="{{ $item->id }}"
                                    class="toggle-status-btn text-yellow-600 hover:text-yellow-900">
                                    {{ $item->status === \App\Enums\ItemStatus::AVAILABLE ? 'Dépublier' : 'Publier' }}
                                </button>
                            @endif
                             <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">Voir</a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                 {{-- Section des offres pour cet item (Desktop) --}}
                @if($item->status === \App\Enums\ItemStatus::AVAILABLE && $item->offers->where('status', 'pending')->isNotEmpty())
                    <tr>
                        <td colspan="4" class="p-0">
                            <x-dashboard.received-offer-list :item="$item" />
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>

<!-- Vue Cartes pour Mobile -->
<div class="sm:hidden space-y-4">
    @foreach ($items as $item)
        <div class="border rounded-lg shadow-lg overflow-hidden">
            <a href="{{ route('items.edit', $item) }}" class="block p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-16 w-16">
                        @if ($item->primaryImage && $item->primaryImage->path)
                            <img class="h-16 w-16 object-cover" src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="{{ $item->title }}">
                        @else
                             <div class="h-16 w-16 bg-gray-200 flex items-center justify-center">
                                <span class="text-xs text-gray-500">?</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-grow">
                        <h4 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h4>
                        <p class="text-sm font-bold text-gray-700 mt-1">{{ number_format($item->price, 2, ',', ' ') }} €</p>
                        <span @class([
                            'mt-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                            'bg-green-100 text-green-800' => $item->status === \App\Enums\ItemStatus::AVAILABLE,
                            'bg-gray-100 text-gray-800' => $item->status === \App\Enums\ItemStatus::UNPUBLISHED,
                            'bg-blue-100 text-blue-800' => $item->status === \App\Enums\ItemStatus::SOLD,
                        ])>
                            @if($item->status === \App\Enums\ItemStatus::AVAILABLE)
                                En ligne
                            @elseif($item->status === \App\Enums\ItemStatus::UNPUBLISHED)
                                Hors ligne
                            @else
                                Vendu
                            @endif
                        </span>
                    </div>
                </div>
            </a>
             <div class="p-4 border-t flex flex-wrap justify-end gap-2 bg-gray-50">
                    @if ($item->status !== \App\Enums\ItemStatus::SOLD)
                        <button
                            data-item-id="{{ $item->id }}"
                            class="toggle-status-btn text-sm bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            {{ $item->status === \App\Enums\ItemStatus::AVAILABLE ? 'Dépublier' : 'Publier' }}
                        </button>
                    @endif
                    <a href="{{ route('items.show', $item) }}" class="text-sm bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300">Voir</a>
                    <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Supprimer</button>
                    </form>
                </div>
             {{-- Section des offres pour cet item (Mobile) --}}
            @if($item->status === \App\Enums\ItemStatus::AVAILABLE && $item->offers->where('status', 'pending')->isNotEmpty())
               <x-dashboard.received-offer-list :item="$item" />
            @endif
        </div>
    @endforeach

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
