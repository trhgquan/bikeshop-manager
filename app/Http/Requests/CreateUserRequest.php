<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    private $validationRules = [
        'name' => 'required|min:6|max:20',
        'username' => 'required|unique:App\Models\User,username|min:6',
        'email' => 'required|unique:App\Models\User,email|email',
        'user_role' => 'required', // Please add role exist here,
        'password' => 'required|min:8',
        're_password' => 'required|same:password'
    ];

    private $validationMessages = [
        'required' => 'Ô :attribute đang bị bỏ trống.',
        'min' => 'Ô :attribute phải có ít nhất :min ký tự.',
        'max' => 'Ô :attribute phải có tối đa :max ký tự.',
        'email' => 'Ô :attribute phải là một địa chỉ email.',
        'unique' => ':attribute đã tồn tại.',
        'same' => 'Giá trị ô :attribute phải trùng với ô :other'
    ];

    private $validationAttributes = [
        'name' => 'Họ và tên',
        'username' => 'Tên người dùng',
        'email' => 'Địa chỉ email',
        'user_role' => 'Quyền',
        'password' => 'Mật khẩu',
        're_password' => 'Nhập lại mật khẩu'
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        // TODO: add authorize here.
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

    public function attributes() {
        return $this->validationAttributes;
    }

    public function messages() {
        return $this->validationMessages;
    }
}
