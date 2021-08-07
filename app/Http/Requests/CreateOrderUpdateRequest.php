<?php

namespace App\Http\Requests;

use App\Http\Requests\CreateOrderRequest;

class CreateOrderUpdateRequest extends CreateOrderRequest
{
    /**
     * Authorization messages for OrderController.
     * 
     * @var array
     */
    private $authorizationMessages = [
        'checked-out' => 'Bạn không được sửa đơn hàng đã thanh toán.',
        'unauthorized' => 'Bạn không có quyền sửa đơn hàng.',
    ];

    /**
     * Throw AuthorizationException if failed to authorize user.
     * 
     * @return \Illuminate\Auth\Access\AithorizationException
     */
    protected function failedAuthorization() {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            $this->authorizationMessages['checked-out']
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        $order = $this->route()->parameter('order');

        // TODO: Require user authorization here.

        return ! $order->getCheckedOut();
    }
}
