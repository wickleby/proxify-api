<?php namespace Proxify\ProxifyApi;

class UserInput
{
    /**
     * @var string
     */
    public $objectName;

    /**
     * @var string
     */
    public $inputOrg;

    /**
     * @var string
     */
    public $input;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->standardized;
    }

    /**
     * Create object form response
     *
     * @param $response
     * @return UserInput
     */
    public static function createFromApiResponse($response)
    {
        $userInput = new self;
        $userInput->objectName = $response['object_name'];
        $userInput->inputOrg = $response['input'];
        $userInput->input = $response['standardized'];

        return $userInput;
    }
}
