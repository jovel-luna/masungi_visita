<?php

namespace App\Http\Requests\Admin\ConservationFees;

use Illuminate\Foundation\Http\FormRequest;

class ConservationFeeStoreRequest extends FormRequest
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

        $rules = [
            'name' => 'required',
            'experience_id' => 'required',
        ];
        
        if (!$id) {
            $rules = array_merge($rules, [
                'special_fee_id' => 'nullable',
                'visitor_type_id' => 'nullable',
                'weekday_fee' => 'nullable',
                'weekend_fee' => 'nullable',
            ]);
        } else {
            $rules = array_merge($rules, [
                'visitor_type_id' => 'required',
                'weekday_fee' => 'required',
                'weekend_fee' => 'required',
            ]);
        }

        return $rules;
    }
}
