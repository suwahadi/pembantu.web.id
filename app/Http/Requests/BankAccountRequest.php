<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_code' => 'required|string|max:10',
            'bank_name' => 'required|string|max:100',
            'account_no' => 'required|string|min:8|max:30|regex:/^[0-9]+$/',
            'account_name' => 'required|string|min:3|max:120',
        ];
    }

    public function messages(): array
    {
        return [
            'account_no.regex' => 'Nomor rekening harus berupa angka',
            'account_name.min' => 'Nama pemilik rekening minimal 3 karakter',
        ];
    }
}
