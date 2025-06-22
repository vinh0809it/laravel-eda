<?php

namespace Src\Presentation\Booking\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property int $car_id
 * @property string $start_date
 * @property string $end_date
 */
class ChangeBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }
} 