<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Plan') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white shadow-md rounded-lg mt-4">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">{{ __('My Subscription') }}</h2>

        @if ($subscription && $subscription->isActive())
            <div class="border p-4 rounded-lg bg-gray-50">
                <p><strong>{{ __('Plan Name') }}:</strong> {{ $subscription->plan_name }}</p>
                <p><strong>{{ __('Plan Price') }}:</strong> ${{ $subscription->plan_price }}</p>
                <p><strong>{{ __('Plan Type') }}:</strong> {{ ucfirst($subscription->plan_type) }}</p>
                <p><strong>{{ __('Start Date') }}:</strong> {{ $subscription->starts_at->format('d M Y') }}</p>
                <p><strong>{{ __('End Date') }}:</strong> {{ $subscription->ends_at->format('d M Y') }}</p>
                <p><strong>{{ __('Status') }}:</strong> 
                    <span class="px-2 py-1 rounded-lg {{ $subscription->status == 'active' ? 'bg-gray-500 text-white' : 'bg-gray-500 text-white' }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </p>

                @if ($subscription->status == 'active')
                    
                    <button id="cancel-subscription" type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 mt-2 rounded">
                        {{ __('Cancel Subscription') }}
                    </button>
                    
                @endif
                <br> <br>
                <a href="{{ route('subscription.plans') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 mt-2 rounded">{{ __('Change Plan') }}</a>
            </div>
        @else
            <p class="text-gray-600">{{ __('no_active_subscription') }}</p>
            <br>
            <a href="{{ route('subscription.plans') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 mt-2 rounded">{{ __('Subscribe Now') }}</a>
        @endif
    </div>

    <script>
        document.getElementById('cancel-subscription').addEventListener('click', function () {
            if (!confirm('Are you sure you want to cancel your subscription?')) return;

            fetch("{{ route('subscription.cancelMyPlan') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Subscription canceled successfully.');
                    location.reload();
                } else {
                    alert(data.error || 'Failed to cancel subscription.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>

</x-app-layout>