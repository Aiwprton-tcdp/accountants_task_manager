<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx|max:100000',
            'department_id' => 'required|int|min:1',
            'contract_type_id' => 'required|int|min:1',
            'payment_period_count' => 'required|int|min:1|max:255',
            'payment_period_type' => 'required|in:d,w,m,q,y',
            'next_payment_date' => 'required|date|date_format:d.m.Y',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_period_count.min' => 'Минимальное число периодов: 1',
            'payment_period_count.max' => 'Максимальное число периодов: 255',
            'file.mimes' => 'Файл должен быть с расширением .pdf .doc или .docx',
            'file.max' => 'Размер файла не должен превышать 100Мб',
        ];
    }
}
