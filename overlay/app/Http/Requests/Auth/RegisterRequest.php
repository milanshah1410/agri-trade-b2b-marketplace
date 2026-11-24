<?php

namespace App\Http\Requests\Auth;

use App\Support\Phone;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $gstin = strtoupper(trim((string) $this->input('gstin')));

        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
            'mobile' => Phone::normalize($this->input('mobile')),
            'gstin' => $gstin === '' ? null : $gstin,
        ]);
    }

    /**
     * Only these fields are ever read from the request: role and status can never be supplied by the client.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'company_name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,mobile'],
            'gstin' => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
            'gstin.regex' => 'Enter a valid 15-character GSTIN.',
        ];
    }
}
