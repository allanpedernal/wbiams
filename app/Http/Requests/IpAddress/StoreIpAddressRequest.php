<?php

namespace App\Http\Requests\IpAddress;

use Illuminate\Foundation\Http\FormRequest;

class StoreIpAddressRequest extends FormRequest
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
            'ip_address' => ['required', 'string', 'max:45', 'ip'],
            'label' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ip_address.required' => 'Please provide an IP address.',
            'ip_address.ip' => 'Please provide a valid IPv4 or IPv6 address.',
            'label.required' => 'Please provide a label for this IP address.',
        ];
    }
}
