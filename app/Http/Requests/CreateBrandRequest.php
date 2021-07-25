<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBrandRequest extends FormRequest
{
    /**
     * Validation rules for LoginController.
     * 
     * @var array
     */
    private $validationRules = [
        'brand_name' => 'required|min:6|max:50',
        'brand_description' => 'required|min:20|max:100'
    ];

    /**
     * Validation messages for LoginController.
     * 
     * @var array
     */
    private $validationMessages = [
        'required' => 'O :attribute bi bo trong.',
        'min' => 'O :attribute phai co do dai it nhat :min ky tu.',
        'max' => 'O :attribute phai co do dai nhieu nhat :max ky tu.'
    ];

    /**
     * Validation attributes for BrandController.
     * 
     * @var array
     */
    private $validationAttributes = [
        'brand_name' => 'Ten hang xe',
        'brand_description' => 'Mo ta hang xe'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return $this->validationRules;
    }

    /**
     * Get the error messages for the defined validation rules.
     * 
     * @return array
     */
    public function messages() {
        return $this->validationMessages;
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * @param array
     */
    public function attributes() {
        return $this->validationAttributes;
    }
}