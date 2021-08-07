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
        'required' => 'Ô :attribute bị bỏ trống.',
        'min' => 'Ô :attribute phải có tối thiểu :min ký tự.',
        'max' => 'Ô :attribute phải có tối đa :max ký tự.'
    ];

    /**
     * Validation attributes for BrandController.
     * 
     * @var array
     */
    private $validationAttributes = [
        'brand_name' => 'Tên hãng xe',
        'brand_description' => 'Mô tả hãng xe'
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