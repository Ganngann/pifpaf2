@props(['item'])

@php
    $pendingOffers = $item->offers->where('status', 'pending');
@endphp

<div class="p-4 border-t bg-gray-50">
    <x-ui.section-title class="text-sm font-semibold text-gray-700">Offres reçues :</x-ui.section-title>

    @if ($pendingOffers->isNotEmpty())
        <ul class="mt-2 space-y-2">
            @foreach ($pendingOffers as $offer)
                <li>
                    <x-ui.offer-card :offer="$offer" viewpoint="seller">
                        <x-slot name="actions">
                            <div class="flex space-x-1">
                                <form action="{{ route('offers.accept', $offer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 text-white px-2 py-1 text-xs rounded hover:bg-green-600">Accepter</button>
                                </form>
                                <form action="{{ route('offers.reject', $offer) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-red-500 text-white px-2 py-1 text-xs rounded hover:bg-red-600">Refuser</button>
                                </form>
                            </div>
                        </x-slot>
                    </x-ui.offer-card>
                </li>
            @endforeach
        </ul>
    @else
        <x-ui.empty-state message="Aucune nouvelle offre pour cet article." />
    @endif
</div>
