<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
            'name' => 'required|string|unique:departments|min:2|max:255',
            'l_l_c_s_id' => 'required|int|min:1|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Отдел с таким названием уже существует',
        ];
    }
}
