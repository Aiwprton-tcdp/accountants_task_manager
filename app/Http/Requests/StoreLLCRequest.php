<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLLCRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'manager_id' => 'required|int|min:1',
            'name' => 'required|string|unique:departments|min:2|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'ООО с таким названием уже существует',
        ];
    }
}
