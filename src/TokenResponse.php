<?php namespace Proxify\ProxifyApi;

use Proxify\ProxifyApi\Exceptions\ProxifyFrameworkException;

class TokenResponse
{
    /**
     * Order id
     *
     * @var int
     */
    public $orderId;

    /**
     * Service id
     *
     * @var int
     */
    public $serviceId;

    /**
     * Current step
     *
     * @var int
     */
    public $currentStep;

    public static function createFromApiResponse($response)
    {
        $tokenResponse = new self;
        $tokenResponse->task = $response['order_id'];
        $tokenResponse->serviceId = $response['service_id'];
        $tokenResponse->currentStep = $response['step_position'];

        if (!$tokenResponse->orderId) {
            throw new ProxifyFrameworkException('Invalid token');
        }

        return $tokenResponse;
    }
}
