<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Validation rules for ChangePasswordController.
     *
     * @var array
     */
    private $validationRules = [
        'password'         => 'required|current_password',
        'new_password'     => 'required|min:8',
        'confirm_password' => 'required|same:new_password',
    ];

    /**
     * Validation message for ChangePasswordController.
     *
     * @var array
     */
    private $validationMessages = [
        'required'         => 'Bạn chưa điền ô :attribute.',
        'current_password' => ':attribute không khớp.',
        'min'              => 'Độ dài ô :attribute phải có độ dài tối thiểu :min ký tự',
        'same'             => 'Ô :attribute phải trùng với ô :other',
    ];

    /**
     * Validation attributes for ChangePasswordController.
     *
     * @var array
     */
    private $validationAttributes = [
        'password'         => 'Mật khẩu hiện tại',
        'new_password'     => 'Mật khẩu mới',
        'confirm_password' => 'Nhập lại mật khẩu mới',
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
     * Get the error messages for the defined validation rules.
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
