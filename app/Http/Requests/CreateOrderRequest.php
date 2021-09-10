<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Validation rules for OrderController.
     *
     * @var array
     */
    protected $validationRules = [
        'customer_name'              => 'required',
        'customer_email'             => 'required|email',
        'order_detail'               => 'required|array',
        'order_detail.*.bike_id'     => 'required|distinct|exists:bikes,id',
        'order_detail.*.order_value' => 'required|numeric|integer|min:1',
    ];

    /**
     * Validation messages for OrderController.
     *
     * @var array
     */
    protected $validationMessages = [
        'array'    => 'Giá trị các ô :attribute không hợp lệ',
        'distinct' => 'Giá trị các ô :attribute phải khác nhau',
        'required' => 'Ô :attribute đang bị để trống.',
        'exists'   => 'Giá trị ô :attribute không hợp lệ.',
        'min'      => 'Giá trị ô :attribute tối thiểu phải là :min',
        'numeric'  => 'Giá trị ô :attribute phải là một số nguyên.',
        'integer'  => 'Giá trị ô :attribute phải là một số nguyên.',
        'email'    => 'Giá trị ô :attribute phải là một địa chỉ email.',
    ];

    /**
     * Validation attributes for OrderController.
     *
     * @var array
     */
    protected $validationAttributes = [
        'customer_name'              => 'Tên khách hàng',
        'customer_email'             => 'Email khách hàng',
        'order_detail'               => 'Chi tiết đơn hàng',
        'order_detail.*.bike_id'     => 'Loại xe',
        'order_detail.*.order_value' => 'Số lượng',
    ];

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
        return $this->validationRules;
    }

    /**
     * Get the error message for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return $this->validationMessages;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->validationAttributes;
    }
}
