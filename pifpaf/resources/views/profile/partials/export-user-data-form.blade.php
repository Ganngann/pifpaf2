<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Export Account Data') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Download an archive of your personal data.') }}
        </p>
    </header>

    <a href="{{ route('profile.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
        {{ __('Download Data') }}
    </a>
</section>
