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
        'bike_description' => 'required|min:20|max:100',
        'bike_stock' => 'required|numeric|integer|min:0',
        'bike_buy_price' => 'required|numeric|integer|min:0',
        'bike_sell_price' => 'required|numeric|integer|min:0'
    ];

    /**
     * Validation messages for BikeController
     * 
     * @var array
     */
    private $validationMessages = [
        'bike_stock.min' => 'O :attribute phai co gia tri toi thieu la 0.',
        'bike_buy_price.min' => 'O :attribute phai co gia tri toi thieu la 0.',
        'bike_sell_price.min' => 'O :attribute phai co gia tri toi thieu la 0.',
        
        'required' => 'O :attribute bi bo trong.',
        'exists' => 'Vui long chon :attribute hop le.',
        'min' => 'O :attribute phai co toi thieu :min ky tu.',
        'max' => 'O :attribute phai co toi da :max ky tu.',
        'numeric' => 'O :attribute phai co gia tri la mot so nguyen.',
        'integer' => 'O :attribute phai co gia tri la mot so nguyen.'
    ];

    /**
     * Validation attributes for BikeController
     * 
     * @var array
     */
    private $validationAttributes = [
        'brand_id' => 'Ten hang',
        'bike_name' => 'Ten loai xe',
        'bike_description' => 'Mo ta loai xe',
        'bike_stock' => 'So luong trong kho',
        'bike_buy_price' => 'Gia nhap',
        'bike_sell_price' => 'Gia ban'
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
