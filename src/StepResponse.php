<?php namespace Proxify\ProxifyApi;

use Proxify\ProxifyApi\Exceptions\EditingCompletedOrderException;
use Proxify\ProxifyApi\Exceptions\MissingOrderIdException;
use Proxify\ProxifyApi\Exceptions\ProxifyFrameworkException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ProxifyFramework
 *
 * Handles the connection with the Proxify Framework
 */
class StepResponse
{
    /**
     * @var int OrderID
     */
    public $orderId;

    /**
     * @var string HTML
     */
    private $content;


    /**
     * @var string Page title
     */
    public $title;

    /**
     * @var string Tracking code
     */
    public $trackingCode;

    /**
     * @var string Error message
     */
    private $errorMessage;

    /**
     * @var string Error code
     */
    private $errorCode;

    /**
     * @var string Action Can be show|redirect|error
     */
    private $action;

    /**
     * @var string
     */
    public $redirectPosition;

    /**
     * StepResponse constructor.
     *
     * @param  ResponseInterface $response
     * @throws EditingCompletedOrderException
     * @throws MissingOrderIdException
     * @throws ProxifyFrameworkException
     */
    public function __construct($response)
    {
        $this->setClassVariablesBasedOnResponse($response);
        $this->fireExceptionsIfThereIsAny();
    }

    /**
     * Set the class variables based on the response
     */
    protected function setClassVariablesBasedOnResponse($response)
    {
        $this->orderId = $response['order_id'] ?? null;
        $this->content = $response['content'] ?? null;
        $this->action = $response['action'];
        $this->errorMessage = $response['error_message'] ?? null;
        $this->errorCode = $response['error_code'] ?? null;
        $this->redirectPosition = $response['step_position'] ?? null;
        $this->trackingCode = $response['tracking_code'] ?? null;
        $this->title = $response['page_title'] ?? null;
    }


    /**
     * Is there an error
     *
     * @return bool
     */
    public function isError()
    {
        return ($this->action == 'error');
    }

    /**
     * Firing exceptions
     *
     * @throws EditingCompletedOrderException
     * @throws MissingOrderIdException
     * @throws ProxifyFrameworkException
     */
    protected function fireExceptionsIfThereIsAny()
    {
        if (!$this->isError()) {
            return;
        }

        // Specific exceptions
        switch ($this->errorCode) {
            case 201:
                throw new EditingCompletedOrderException($this->errorMessage, $this->errorCode);
                break;
            case 202:
                throw new MissingOrderIdException($this->errorMessage, $this->errorCode);
                break;
        }

        // Other exceptions
        throw new ProxifyFrameworkException($this->errorMessage, $this->errorCode);
    }


    public function isRedirectRequired()
    {
        return ($this->action == 'redirect');
    }

    public function getPositionOfNextStep()
    {
        return $this->redirectPosition;
    }

    public function getContent()
    {
        return self::addCsrfTokenToForm(self::getAllBeforeJsStart($this->content));
    }

    public function getJs()
    {
        return self::getAllAfterJsStart($this->content);
    }

    /**
     * Get all HTML code but the JS code
     *
     * @param $htmlCode
     * @return string
     */
    private static function getAllBeforeJsStart($htmlCode)
    {
        $result = explode('<!-- JS START -->', $htmlCode);

        return $result[0];
    }

    /**
     * Get all JS code from the HTML code (mentioned in the bottom)
     *
     * @param string $htmlCode
     * @return string HTML code, most Javascript
     */
    private static function getAllAfterJsStart($htmlCode)
    {
        $result = explode('<!-- JS START -->', $htmlCode);

        return $result[1] ?? '';
    }

    /**
     * Append the CSRF field to the form
     *
     * @param string $content
     * @return string
     */
    private static function addCsrfTokenToForm($content)
    {
        return str_replace('</form>', csrf_field() . '</form>', $content);
    }
}
