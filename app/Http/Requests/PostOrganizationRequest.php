<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostOrganizationRequest extends FormRequest
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
        $rules = [
            'org_name' => 'required|max:255|unique:organizations,name,NULL,id,parent_id,NULL',
            'daughters' => 'sometimes|required|array'
        ];
        return $rules;
    }
}
