<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreManagerRequest extends FormRequest
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
            'crm_id' => 'required|int|unique:managers|min:1',
            'name' => 'required|string|min:2',
        ];
    }

    public function messages(): array
    {
        return [
            'crm_id.unique' => 'Данный пользователь уже является участником',
        ];
    }
}