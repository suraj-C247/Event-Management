<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            @if(auth()->user()->role === 'admin')
                {{ __('Admin Dashboard') }}
            @else
                {{ __('User Dashboard') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if (session('success') && session('plan_data'))
                    <div class="text-green p-4 mb-4">
                        {{ session('success') }}
                    </div>
                    <div class="bg-green-100 p-4 rounded-md shadow-md">
                        <h2 class="text-lg font-bold">{{ __('Subscription Details') }}</h2>
                        <p><strong>{{ __('Plan Name') }}:</strong> {{ session('plan_data.plan_name') }}</p>
                        <p><strong>{{ __('Plan Price') }}:</strong> ${{ session('plan_data.plan_price') }}</p>
                        <p><strong>{{ __('Plan Duration') }}:</strong> {{ session('plan_data.plan_duration') }} {{ session('plan_data.plan_type') }}</p>
                        <p><strong>{{ __('Max Events') }}:</strong> {{ session('plan_data.max_events') }}</p>
                    </div>
                @elseif (session('success'))
                    <div class="text-green p-4 mb-4">
                        {{ session('success') }}
                    </div>
                @elseif (session('error'))
                    <div class="text-red p-4 mb-4">
                        {{ session('error') }}
                    </div>
                @else
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                @endif

                @if(auth()->user()->role === 'user')
                    @if(isset($subscription) && $subscription)
                        <div class="bg-green-100 p-4 rounded-md shadow-md">
                            <h2 class="text-lg font-bold">{{ __('Current Subscription') }}</h2>
                            <p><strong>{{ __('Plan Name') }}:</strong> {{ $subscription->plan_name }}</p>
                            <p><strong>{{ __('Plan Price') }}:</strong> ${{ $subscription->plan_price }}</p>
                            <p><strong>{{ __('Plan Duration') }}:</strong> {{ $subscription->plan_duration }} {{ $subscription->plan_type }}</p>
                            <p><strong>{{ __('Max Events') }}:</strong> {{ $subscription->max_events }}</p>
                            <br>
                            <a href="{{ route('subscription.plans') }}" class="px-3 py-2 bg-gray-500 text-white rounded">{{ __('Change Plan') }}</a>
                        </div>
                    
                    @else
                        <div class="bg-red-100 p-4 rounded-md shadow-md">
                            <h2 class="text-lg font-bold">{{ __('No Subscription') }}</h2>
                            <br>
                            <a href="{{ route('subscription.plans') }}" class="px-3 py-2 bg-gray-500 text-white rounded">{{ __('Subscribe Now') }}</a>
                        </div>
                        
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
