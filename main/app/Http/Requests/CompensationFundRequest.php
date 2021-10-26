<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompensationFundRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'unique:compensation_funds,code',
            'nit' => 'unique:compensation_funds,nit'
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'El código se encuentra registrado',
            'nit.unique' => 'El NIT se encuentra registrado'
        ];
    }

}
