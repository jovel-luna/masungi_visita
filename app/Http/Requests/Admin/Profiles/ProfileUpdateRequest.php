<?php

namespace App\Http\Requests\Admin\Profiles;

use Illuminate\Foundation\Http\FormRequest;

use App\Rules\Varchar;
use App\Rules\Image;

class ProfileUpdateRequest extends FormRequest
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
            'first_name' => ['required', new Varchar],
            'last_name' => ['required', new Varchar],
            'email' => 'required|email|unique:admins,email,' . $this->user()->id,
            'image_path' => ['nullable', new Image],
        ];
    }
}
