<?php

namespace Src\Booking\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $completion_note
 */
class CompleteBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'completion_note' => 'required|string|max:1000',
        ];
    }
}
