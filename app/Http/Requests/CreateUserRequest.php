<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Validation messages for UserManagementController.
     *
     * @var array
     */
    private $validationMessages = [
        'required' => 'Ô :attribute đang bị bỏ trống.',
        'min'      => 'Ô :attribute phải có ít nhất :min ký tự.',
        'max'      => 'Ô :attribute phải có tối đa :max ký tự.',
        'email'    => 'Ô :attribute phải là một địa chỉ email.',
        'unique'   => ':attribute đã tồn tại.',
        'same'     => 'Giá trị ô :attribute phải trùng với ô :other',
        'exists'   => 'Giá trị ô :attribute không hợp lệ.',
    ];

    /**
     * Validation attributes for UserManagementController.
     *
     * @var array
     */
    private $validationAttributes = [
        'name'        => 'Họ và tên',
        'username'    => 'Tên người dùng',
        'email'       => 'Địa chỉ email',
        'user_role'   => 'Quyền',
        'password'    => 'Mật khẩu',
        're_password' => 'Nhập lại mật khẩu',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO: add authorize here.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Define here since we can't execute function inside an attribute.
        return [
            'name'     => 'required|min:6|max:20',
            'username' => 'required|unique:App\Models\User,username|min:6',
            'email'    => 'required|unique:App\Models\User,email|email',
            'role'     => [
                'required',
                'exists:App\Models\Role,id',
                Rule::in([
                    Role::ROLE_MANAGER,
                    Role::ROLE_STAFF,
                ]),
            ],
            'password'    => 'required|min:8',
            're_password' => 'required|same:password',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->validationAttributes;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return $this->validationMessages;
    }
}
