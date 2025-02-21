<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>
    
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('Name') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Email') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Status') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    @if($users->isEmpty())
                        <tr>
                            <td class="px-6 py-4 text-center" colspan="4">{{ __('no_records') }}</td>
                        </tr>
                    @else
                        @foreach ($users as $user)
                            <tr>
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $user->name }}</td>
                                <td class="px-6 py-4">{{ $user->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="status-{{ $user->id }}">
                                        {{ $user->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="px-3 py-2 bg-gray-500 text-white rounded" type="button" id="toggle-btn-{{ $user->id }}" onclick="changeStatus({{ $user->id }})">
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="px-6 py-4">
            {{ $users->links() }}
        </div>

        <script>

            //Laravel translations to JavaScript
            const translations = {
                confirmChangeStatus: "{{ __('user_status_change') }}",
                active: "{{ __('Active') }}",
                inactive: "{{ __('Inactive') }}",
                activate: "{{ __('Activate') }}",
                deactivate: "{{ __('Deactivate') }}"
            };

            function changeStatus(userId) {
                let confirmation =  confirm(translations.confirmChangeStatus);
                if(!confirmation)
                {
                   return false; 
                }

                fetch("{{ route('admin.changeStatus') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ user_id: userId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let statusSpan = document.querySelector(`.status-${userId}`);
                        let button = document.getElementById(`toggle-btn-${userId}`);
                        statusSpan.textContent = data.status ? translations.active : translations.inactive;
                        button.textContent = data.status ? translations.deactivate : translations.activate;
                    }
                });
            }

        </script>
   
</x-app-layout>
