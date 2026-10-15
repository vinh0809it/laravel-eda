<?php

namespace Src\Booking\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $cancel_reason
 */
class CancelBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => 'required|string|max:255',
        ];
    }
}
