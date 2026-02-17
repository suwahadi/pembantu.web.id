<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_scope' => 'required|string|min:20|max:1000',
            'location_address' => 'required|string|min:10|max:500',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'terms_conditions' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'job_scope.required' => 'Scope pekerjaan harus diisi',
            'job_scope.min' => 'Scope pekerjaan minimal 20 karakter',
            'location_address.required' => 'Alamat lokasi harus diisi',
            'start_date.required' => 'Tanggal mulai harus diisi',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ];
    }
}
