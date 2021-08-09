<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Validation rules for LoginController.
     * 
     * @var array
     */
    private $validationRules = [
        'username' => 'required',
        'password' => 'required'
    ];

    /**
     * Validation messages for LoginController.
     * 
     * @var array
     */
    private $validationMessages = [
        'username.required' => 'Tên người dùng đang bỏ trống.',
        'password.required' => 'Mật khẩu đang bỏ trống.'
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
}