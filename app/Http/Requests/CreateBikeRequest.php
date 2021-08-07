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
        'bike_stock.min' => 'Ô :attribute phải có giá trị tối thiểu là 0.',
        'bike_buy_price.min' => 'Ô :attribute phải có giá trị tối thiểu là 0.',
        'bike_sell_price.min' => 'Ô :attribute phải có giá trị tối thiểu là 0.',
        
        'required' => 'Ô :attribute bị bỏ trống.',
        'exists' => 'Vui lòng chọn :attribute hợp lệ.',
        'min' => 'Ô :attribute phải có tối thiểu :min ký tự.',
        'max' => 'Ô :attribute phải có tối đa :max ký tự.',
        'numeric' => 'Ô :attribute phải có giá trị là một số nguyên.',
        'integer' => 'Ô :attribute phải có giá trị là một số nguyên.'
    ];

    /**
     * Validation attributes for BikeController
     * 
     * @var array
     */
    private $validationAttributes = [
        'brand_id' => 'Tên hãng',
        'bike_name' => 'Tên loại xe',
        'bike_description' => 'Mô tả loại xe',
        'bike_stock' => 'Số lượng',
        'bike_buy_price' => 'Giá nhập',
        'bike_sell_price' => 'Giá bán'
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
