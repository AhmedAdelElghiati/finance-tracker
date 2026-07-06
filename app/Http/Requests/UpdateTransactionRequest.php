<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes' ,'required' , 'string' , 'min:3' , 'max:255'],
            'description' => [ 'sometimes' ,'string' , 'nullable' , 'max:2000'],
            'amount' => ['sometimes' ,'required' , 'numeric' , 'min:0.01'],
            'type' => ['sometimes' , 'required' , 'in:income,expense'],
            'category' => ['sometimes' ,'required' , 'string' , 'max:100'],
            'date' => ['sometimes' ,'required' , 'date']
        ];
    }
}
