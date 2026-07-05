<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHeaterLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'heater_code' => 'required|string',
            'current' => 'required|numeric|min:0',
            'voltage' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'adc_value' => 'nullable|integer'
        ];
    }
}