<?php

namespace App\Http\Requests\Admin\TrainingModules;

use Illuminate\Foundation\Http\FormRequest;

class TrainingModuleStoreRequest extends FormRequest
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

        $id = $this->route('id');

        return [
            'title' => 'required',
            'destination_id' => 'required',
            'description' => 'required',
            'path' => $id ? '' : 'required'
            // 'path' => 'required|mimes:jpeg,jpg,png,mp4,ogg,3gp,wmv,mov,avi'
        ];
    }

    public function messages() {
        return [
            'destination_id.required' => 'Please select a destination',
            'path.required' => 'File is required',
            // 'path.mimes' => 'File is format is invalid (Accepted Format jpeg,jpg,png,mp4,ogg,3gp,wmv,mov and avi).'
        ];
    }
}
