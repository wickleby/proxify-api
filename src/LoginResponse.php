<?php namespace Proxify\ProxifyApi;

class LoginResponse
{
    /**
     * @var bool
     */
    public $authorized;

    /**
     * @var int
     */
    public $orderId;

    public function __construct($apiResponse)
    {
        $this->authorized = false;

        if (isset($apiResponse['order_id'])) {
            $this->orderId = $apiResponse['order_id'];
            $this->authorized = true;
        }
    }
}
