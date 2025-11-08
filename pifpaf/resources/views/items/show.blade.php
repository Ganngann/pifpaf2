<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        {{-- Breadcrumb --}}
        <div class="mb-4 text-sm text-gray-600">
            <a href="{{ route('welcome') }}" class="hover:underline">Accueil</a> >
            <a href="{{ route('welcome', ['category' => $item->category]) }}" class="hover:underline">{{ $item->category }}</a> >
            <span>{{ $item->title }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
            <!-- Colonne de gauche (plus large) -->
            <div class="md:col-span-3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <x-items.gallery :item="$item" />
                </div>

                <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4 border-b pb-2">Description détaillée</h2>
                    <x-items.description :description="$item->description" />
                </div>
            </div>

            <!-- Colonne de droite (plus étroite) -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-8" x-data="{ deliveryMethod: '' }">
                    <x-items.header :item="$item" />

                    <div class="mt-6 border-t pt-6">
                        <x-items.delivery-methods :item="$item" />
                    </div>

                    <div class="mt-6 border-t pt-6">
                        <x-items.actions :item="$item" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main-layout>
