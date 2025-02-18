<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2">
            {{ __('Create Plan') }}
        </h2>
        <a href="{{ route('admin.plans') }}" class="px-3 py-2 bg-gray-500 text-white rounded">
            {{ __('Plan List') }}
        </a>
    </x-slot>

    <div class="max-w-2xl mx-auto mb-6 px-6 py-4">
        <form action="{{ route('admin.plans.store') }}" method="POST" class="max-w-sm mx-auto" id="planForm">
            @csrf
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('Plan Name') }}
                </label>
                <input type="text" name="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('name') }}">
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('Price ($)') }}
                </label>
                <input type="number" name="price" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('price') }}">
                <x-input-error :messages="$errors->get('price')" class="mt-2" />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('Plan Type') }}
                </label>
                <select name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="day">{{ __('Daily') }}</option>
                    <option value="week">{{ __('Weekly') }}</option>
                    <option value="month">{{ __('Monthly') }}</option>
                    <option value="year">{{ __('Yearly') }}</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Plan Description') }}</label>
                <textarea id="description" name="description" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Max Events') }}</label>
                <input type="number" name="max_events" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('max_events') }}">
                <x-input-error :messages="$errors->get('max_events')" class="mt-2" />
            </div>

            <button type="submit" class="px-3 py-2 bg-gray-500 text-white rounded mt-3">
                {{ __('Create Plan') }}
            </button>
        </form>
    </div>
    <script src="{{ asset('js/plan.js') }}"></script>
</x-app-layout>

