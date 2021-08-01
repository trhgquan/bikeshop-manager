<?php

namespace App\Http\Requests;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Bike;

class CreateOrderUpdateRequest extends CreateOrderRequest
{
    private $authorizationMessages = [
        'checked-out' => 'Ban khong sua duoc don hang da thanh toan.',
        'unauthorized' => 'Ban khong co quyen sua don hang.',
    ];

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
