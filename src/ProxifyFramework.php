<?php namespace Proxify\ProxifyApi;

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Proxify\ProxifyApi\Exceptions\ProxifyFrameworkException;
use Proxify\ProxifyApi\Exceptions\ServerException;

/**
 * Class ProxifyFramework
 *
 * Handles the connection with the Proxify Framework to generate steps etc.
 */
class ProxifyFramework
{

    protected $locale = 'es';

    protected $domain;

    /**
     * Set locale
     *
     * @param $locale string
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Set the domain of the request
     *
     * @param $domain string eg. abcdivorcio.es
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

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
     * Get all tasks and their current status for an order
     *
     * @param $orderId
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getTasks($orderId)
    {
        $response = $this->getRequest('order/' . $orderId . '/tasks');

        return Tasks::createFromApiResponse($response);
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
     * @throws ProxifyFrameworkException
     * @throws \Proxify\ProxifyApi\SeverException
     */
    private function apiRequest($urn, $method, $params = [])
    {
        $options = [
            'auth' => [$this->getSetting('services.proxify.user'), $this->getSetting('services.proxify.pass')],
        ];

        $params = array_merge($params, [
            'locale' => $this->locale,
            'domain' => $this->domain
        ]);

        if ($method == 'GET') {
            $options['query'] = $params;
        } elseif ($method == 'POST') {
            $options['body'] = json_encode($params);
        }

        $client = new Client(['headers' => ['Content-Type' => 'application/json']]);

        try {
            $response = $client->request(
                $method, $this->getSetting('services.proxify.api_root') . DIRECTORY_SEPARATOR . $urn,
                $options);
        } catch (ServerException $e) {
            throw new ServerException('Server error');
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Get config
     *
     * And validate there are existing
     *
     * @param $name string
     * @return mixed
     * @throws ProxifyFrameworkException
     */
    private function getSetting($name)
    {
        $config = Config::get($name);

        if (empty($config)) {
            throw new ProxifyFrameworkException("Required config ($name) is missing");
        }

        return $config;
    }
}
