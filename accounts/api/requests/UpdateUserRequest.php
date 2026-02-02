<?php

namespace accounts\api\requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Add authorization logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $this->route('id'),
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:customer,vendor,admin',
            'status' => 'sometimes|in:active,inactive,pending',
            'vendor_id' => 'sometimes|nullable|integer|exists:vendors,id'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name cannot be longer than 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.min' => 'Password must be at least 8 characters long.',
            'role.in' => 'Role must be one of: customer, vendor, admin.',
            'status.in' => 'Status must be one of: active, inactive, pending.',
            'vendor_id.exists' => 'The selected vendor does not exist.'
        ];
    }

    /**
     * Get the validated data from the request.
     */
    public function getValidatedData(): array
    {
        return $this->validated();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare data before validation
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name)
            ]);
        }
    }
}