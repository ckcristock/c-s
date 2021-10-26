<?php

namespace App\Http\Requests;

use App\Models\Arl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArlRequest extends FormRequest
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
                'nit' => 'unique:arl,nit'
            ];
    }

    public function messages()
    {
        return [
            'nit.unique' => 'El NIT se encuentra registrado'
        ];
    }
}
