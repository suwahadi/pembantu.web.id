<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'complaint.required' => 'Keluhan harus diisi',
            'complaint.min' => 'Keluhan minimal 10 karakter',
            'complaint.max' => 'Keluhan maksimal 1000 karakter',
        ];
    }
}
