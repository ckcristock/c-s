<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PensionFundRequest extends FormRequest
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
            'code' => 'unique:pension_funds,code',
            'nit' => 'unique:pension_funds,nit'
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'El código se encuentra registrado',
            'nit.unique' => 'El NIT se encuentra registrado'
        ];
    }

    /**
     * Get the underlying response instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse()
    {
        return $this->error('Formato no válido', 402);
    }
}
