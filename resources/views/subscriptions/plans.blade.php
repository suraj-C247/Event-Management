<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2">
            {{ __('Choose a Subscription Plan') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 py-4">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
                <form action="" method="POST" class="bg-white p-6 shadow-lg rounded-lg border text-center">
                    <h3 class="text-lg font-semibold mb-2">{{ $plan->name }}</h3>
                    <p class="text-gray-600 font-medium">{{ $plan->price }} {{ config('global.currency') }} / {{ ucfirst($plan->type) }} <br> {{ $plan->max_events }} {{ __('Max Events') }}</p>
                    <button type="button" class="mt-4 w-full bg-gray-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-gray-600 transition" onclick="checkout({{ $plan->id }})">
                        {{ __('Subscribe') }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    <script>
        function checkout(planId) {
            fetch("{{ route('subscription.checkout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ plan_id: planId })
            })
            .then(response => response.json())
            .then(data => {
                console.log('checkout data', data);
                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
</x-app-layout>
