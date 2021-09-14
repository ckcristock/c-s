<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EpsRequest extends FormRequest
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
            'nit' => 'unique:App\Models\Eps,nit',
            'code' => 'unique:epss,code'
        ];
    }

    public function messages()
    {
        return [
            'nit.unique' => 'El NIT se encuentra registrado',
            'code.unique' => 'El código se encuentra registrado',
        ];
    }
}
