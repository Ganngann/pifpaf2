<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $type === 'pickup' ? __('Ajouter une nouvelle adresse de retrait') : __('Ajouter une nouvelle adresse de livraison') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('profile.addresses.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        @include('profile.addresses._form', ['address' => new \App\Models\Address(), 'type' => $type])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
