<x-guest-layout>
    
    @if (session('success'))
        <div class="text-green p-4 mb-4">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="text-red p-4 mb-4">
            {{ session('error') }}
        </div>
    @else
        <!-- Redirect to Home Page after 3 seconds -->
        <meta http-equiv="refresh" content="3;url={{ url('/') }}">
        <p class="text-gray-600">Redirecting you to the home page...</p>
    @endif

</x-guest-layout>
