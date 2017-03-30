<?php namespace Proxify\ProxifyApi;

class Order
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $token;

    /**
     * @var Service
     */
    public $service;

    /**
     * @var string
     */
    public $status;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $payment_method;

    /**
     * @var string
     */
    public $flag_description;

    /**
     * @var UserInput[]
     */
    public $userInputs = [];

    public static function createFromApiResponse($response)
    {
        $order = new self;
        $order->id = $response['id'];
        $order->token = $response['token'];
        $order->status = $response['status'];
        $order->price = (float)$response['price'];
        $order->currency = $response['currency'];
        $order->payment_method = $response['payment_method'];
        $order->flag_description = $response['flag_description'];

        $order->service = Service::createFromApiResponse($response['service']);

        foreach ($response['user_inputs'] as $userInputResponse) {
            $order->userInputs[] = UserInput::createFromApiResponse($userInputResponse);
        }

        return $order;
    }

    public function input($objectName, $defaultValue = null)
    {
        foreach ($this->userInputs as $userInput) {
            if ($userInput->objectName == $objectName) {
                return $userInput->input;
            }
        }

        return $defaultValue;
    }

    public function inputOrg($objectName, $defaultValue = null)
    {
        foreach ($this->userInputs as $userInput) {
            if ($userInput->objectName == $objectName) {
                return $userInput->input;
            }
        }

        return $defaultValue;
    }
}
