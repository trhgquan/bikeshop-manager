<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Bike;

class CreateOrderRequest extends FormRequest
{
    /**
     * Validation rules for OrderController.
     * 
     * @var array
     */
    protected $validationRules = [
        'customer_name' => 'required',
        'customer_email' => 'required|email',
        'bike_id.*' => 'distinct|exists:bikes,id',
        'order_value.*' => 'required|numeric|integer|min:1'
    ];

    /**
     * Validation messages for OrderController.
     * 
     * @var array
     */
    protected $validationMessages = [
        'distinct' => 'Gia tri cac o :attribute phai khac nhau',
        'required' => 'O :attribute dang bi de trong.',
        'exists' => 'Gia tri o :attribute khong hop le.',
        'min' => 'Gia tri o :attribute toi thieu phai la :min',
        'numeric' => 'Gia tri o :attribute phai la mot so nguyen.',
        'integer' => 'Gia tri o :attribute phai la mot so nguyen.',
        'email' => 'Gia tri o :attribute phai la mot dia chi email.'
    ];

    /**
     * Validation attributes for OrderController.
     * 
     * @var array
     */
    protected $validationAttributes = [
        'customer_name' => 'Ten khach hang',
        'customer_email' => 'Email khach hang',
        'bike_id.*' => 'Loai xe',
        'order_value.*' => 'So luong'
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
