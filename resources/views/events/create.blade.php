<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-2">
        {{ __('Create Event') }}
        </h2>
        <a href="{{ route('events.index') }}" class="px-3 py-2 bg-gray-500 text-white rounded">{{ __('Event List') }}</a>
    </x-slot>

    <div class="md:flex md:justify-center mb-6 px-6 py-4">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto" id="eventForm">
            @csrf
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Event Date') }}</label>
                <input type="datetime-local" name="event_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('event_date') }}">
                <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
            </div>
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Event Title') }}</label>
                <input type="text" name="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('title') }}">
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Event Description') }}</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Event Venue') }}</label>
                <input type="text" name="venue" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" value="{{ old('venue') }}">
                <x-input-error :messages="$errors->get('venue')" class="mt-2" />
            </div>
            <div class="mb-5">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Event Image') }}</label>
                <input type="file" id="imageInput" name="image" accept="image/*" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <input type="hidden" name="cropped_image" id="croppedImageData" value="{{ old('cropped_image') }}">
                <x-input-error :messages="$errors->get('cropped_image')" class="mt-2" />
                <!-- Image Preview -->
                <div id="previewContainer" class="mt-4 hidden">
                    <p class="text-gray-600">{{ __('Cropped Image Preview') }}:</p>
                    <img id="croppedImagePreview" class="mt-2 w-32 h-32 object-cover border rounded shadow" src="{{ old('cropped_image') }}" alt="Cropped Image">
                </div>
            </div>
            <button type="submit" class="px-3 py-2 bg-gray-500 text-white rounded mt-3">{{ __('Create Event') }}</button>
        </form>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden" style="z-index: 1;">
        <div class="bg-white rounded-lg p-5 w-[90%] max-w-md">
            <h2 class="text-lg font-semibold mb-3">{{ __('Crop Image') }}</h2>
            <div class="w-full h-[300px] overflow-hidden">
                <img id="modalImage" class="w-full">
            </div>
            <div class="flex justify-end mt-4">
                <button id="cancelCrop" class="px-4 py-2 bg-gray-500 text-white rounded mr-2">{{ __('Cancel') }}</button>
                <button id="cropButton" class="px-4 py-2 bg-gray-500 text-white rounded">{{ __('Crop & Save') }}</button>
            </div>
        </div>
    </div>

    <!-- Include Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.0.0/tinymce.min.js"></script>
    <script>
        window.appConfig = {
            eventDateMin: {{ config('global.event.date_min') }},
            imageMaxSize: {{ config('global.event.image_max_size') }},
            imageSizeErrorMsg: @json(__('image_size_error')),
            eventDateErrorMsg: @json(__('event_date_error')),
            eventDateRequiredMsg: @json(__('event_date_required')),
            titleRequiredMsg: @json(__('title_required')),
            titleMaxlengthMsg: @json(__('title_maxlength')),
            descriptionRequiredMsg: @json(__('description_required')),
            descriptionMaxlengthMsg: @json(__('description_maxlength')),
            venueRequiredMsg: @json(__('venue_required')),
            venueMaxlengthMsg: @json(__('venue_maxlength')),
            cropImgRequiredMsg: @json(__('cropped_image_required')),
        };
    </script>
    <script src="{{ asset('js/event.js') }}"></script>

</x-app-layout>
