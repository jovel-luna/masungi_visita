<?php

namespace App\Http\Requests\Admin\Agencies;

use Illuminate\Foundation\Http\FormRequest;

class AgencyStoreRequest extends FormRequest
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
            'code' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'contact_person' => 'required',
            'contact_number' => 'required|max:11',
        ];
    }
}
