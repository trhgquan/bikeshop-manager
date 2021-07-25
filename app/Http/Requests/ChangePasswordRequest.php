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
        'password' => 'required|current_password',
        'new_password' => 'required|min:8',
        'confirm_password' => 'required|same:new_password'
    ];

    /**
     * Validation message when fails.
     * 
     * @var array
     */
    private $validationMessages = [
        'required' => 'Ban chua dien o :attribute',
        'current_password' => 'Mat khau hien tai khong khop',
        'min' => 'Do dai o :attribute phai toi thieu :min ky tu',
        'same' => 'O :attribute khong trung voi o :other'
    ];

    /**
     * Validation attributes.
     * 
     * @var array
     */
    private $validationAttributes = [
        'password' => 'Mat khau hien tai',
        'new_password' => 'Mat khau moi',
        'confirm_password' => 'Nhap lai mat khau moi'
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