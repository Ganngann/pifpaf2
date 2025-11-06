<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Export Account Data') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Export all of your account data to a JSON file.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.export') }}">
        @csrf
        <x-primary-button>
            {{ __('Export Data') }}
        </x-primary-button>
    </form>
</section>
