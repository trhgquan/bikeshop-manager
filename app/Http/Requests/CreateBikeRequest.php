<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBikeRequest extends FormRequest
{
    /**
     * Validation rules for BikeController
     * 
     * @var array
     */
    private $validationRules = [
        'brand_id' => 'required|exists:brands,id',
        'bike_name' => 'required|min:6|max:20',
        'bike_description' => 'required|min:20|max:100'
    ];

    /**
     * Validation messages for BikeController
     * 
     * @var array
     */
    private $validationMessages = [
        'required' => 'O :attribute bi bo trong.',
        'exists' => 'Vui long chon :attribute hop le.',
        'min' => 'O :attribute phai co toi thieu :min ky tu',
        'max' => 'O :attribute phai co toi da :max ky tu'
    ];

    /**
     * Validation attributes for BikeController
     * 
     * @var array
     */
    private $validationAttributes = [
        'brand_id' => 'Ten hang',
        'bike_name' => 'Ten loai xe',
        'bike_description' => 'Mo ta loai xe'
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
     * Get the error message for the defined validation rules.
     * 
     * @return array
     */
    public function messages() {
        return $this->validationMessages;
    }

    /**
     * Get custom attributes for validator errors.
     * 
     * @return array
     */
    public function attributes() {
        return $this->validationAttributes;
    }
}
