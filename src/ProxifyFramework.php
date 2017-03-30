<?php namespace Proxify\ProxifyApi;

use Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

/**
 * Class ProxifyFramework
 *
 * Handles the connection with the Proxify Framework to generate steps etc.
 */
class ProxifyFramework
{
    /**
     * Get order
     *
     * @param $orderId
     * @return Order
     */
    public function getOrder($orderId)
    {
        $response = $this->getRequest('order/' . $orderId);

        return Order::createFromApiResponse($response);
    }


    public function token($token)
    {
        $response = $this->getRequest('token/' . $token);

        return TokenResponse::createFromApiResponse($response);
    }


    /**
     * Get the step
     *
     * @param int $stepPosition Step position
     * @param int $serviceId
     * @param int|null $orderId
     * @return StepResponse
     */
    public function getStep($stepPosition, $serviceId, $orderId)
    {
        $options = [
            'step_position' => $stepPosition,
            'service_id' => $serviceId,
            'order_id' => $orderId,
        ];

        return new StepResponse($this->getRequest('step', $options));
    }


    /**
     * Send user input
     *
     * @param $stepPosition
     * @param $orderId
     * @param Request $request
     * @return StepResponse
     */
    public function sendStep($stepPosition, $serviceId, $orderId, Request $request)
    {
        $options = [
            'step_position' => $stepPosition,
            'service_id' => $serviceId,
            'order_id' => $orderId,
        ];

        $options = array_merge($options, $request->all());

        $response = $this->postRequest('step', $options);

        return new StepResponse($response);
    }


    /**
     * Login a user to the tracker
     *
     * @param $email string
     * @param $password string
     * @return LoginResponse
     */
    public function login($email, $password)
    {

        try {
            $response = $this->postRequest('login', compact('email', 'password'));
        } catch (ClientException $e) {
            $response = false;
        }

        return new LoginResponse($response);
    }


    /**
     * Send a get request
     *
     * @param string $urn URN
     * @param array $params Query params
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function getRequest($urn, $params = [])
    {
        return $this->apiRequest($urn, 'GET', $params);
    }

    /**
     * Send a post request
     *
     * @param string $urn URN
     * @param array $params Query params
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function postRequest($urn, $params = [])
    {
        return $this->apiRequest($urn, 'POST', $params);
    }

    /**
     * Send a request to the API
     *
     * @param string $urn Urn
     * @param string $method
     * @param array $params Query parameters
     * @return array Json
     */
    private function apiRequest($urn, $method, $params = [])
    {
        $options = [
            'auth' => [Config::get('services.proxify.user'), Config::get('services.proxify.pass')]
        ];

        if ($method == 'GET') {
            $options['query'] = $params;
        } elseif ($method == 'POST') {
            $options['body'] = json_encode($params);
        }

        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);
        $response = $client->request($method, Config::get('services.proxify.api_root') . DIRECTORY_SEPARATOR . $urn,
            $options);


        return json_decode($response->getBody(), true);
    }
}
