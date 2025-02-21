<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'event_date' => ['required', 'date', 'after_or_equal:' . now()->addMinutes(config('global.event.date_min'))->format('Y-m-d H:i')],
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:500',
            'cropped_image' => ['required','regex:/^data:image\/(jpeg|png|jpg|gif);base64,/'], // Base64 image validation
            'venue' => 'required|string|max:100'
        ];
    }
}
