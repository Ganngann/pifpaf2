<x-main-layout>
    <div class="container mx-auto px-4 py-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('success')" />

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <x-items.gallery :item="$item" />

            <div class="p-8">
                <x-items.header :item="$item" />

                <x-items.description :description="$item->description" />

                <x-items.delivery-methods :item="$item" />

                <x-items.actions :item="$item" />
            </div>
        </div>
    </div>
</x-main-layout>
