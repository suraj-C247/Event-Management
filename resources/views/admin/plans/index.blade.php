<x-app-layout>
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2">
            {{ __('Plans') }}
        </h2>
        <a href="{{ route('admin.plans.create') }}" class="px-3 py-2 bg-gray-500 text-white rounded">{{ __('Create Plans') }}</a>
    </x-slot>
    <!-- Check for success message -->
        @if (session('success'))
            <div class="text-green p-4 mb-4">
                {{ session('success') }}
            </div>
        @endif
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">{{ __('Plan Name') }}</th>
                    <th class="px-6 py-3">{{ __('Plan Price') }}</th>
                    <th class="px-6 py-3">{{ __('Plan Type') }}</th>
                    <th class="px-6 py-3">{{ __('Max Events') }}</th>
                </tr>
            </thead>
            <tbody class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                @foreach ($plans as $plan)
                    <tr>
                        <td class="px-6 py-4">{{ $plan->name }}</td>
                        <td class="px-6 py-4">{{ $plan->price }} {{ config('global.currency') }}</td>
                        <td class="px-6 py-4">{{ $plan->duration }} {{ ucfirst($plan->type) }}</td>
                        <td class="px-6 py-4">{{ $plan->max_events }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4">
        {{ $plans->links() }}
    </div>
</x-app-layout>
