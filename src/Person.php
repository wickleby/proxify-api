<?php namespace Proxify\ProxifyApi;

/**
 * Class Person
 * @package Proxify\ProxifyApi
 */
class Person extends ProxifyFramework
{
    /**
     * @var SwedishSecurityNumber
     */
    public $ssn;

    /**
     * @var string First name
     */
    public $firstName;

    /**
     * @var string Spoken first name
     */
    public $spokenName;



    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string male|female
     */
    public $gender;


    /**
     * @var Address
     */
    public $address;


    /**
     * Create from API
     *
     * @param $response array
     * @return Person
     */
    public static function createFromApiResponse($response)
    {
        $person = new self;
        $person->ssn = SwedishSecurityNumber::id($response['ssn']);
        $person->firstName = $response['firstName'];
        $person->spokenName = $response['spokenName'];
        $person->lastName = $response['lastName'];
        $person->gender = $response['gender'];
        $person->address = Address::createFromApiResponse($response['address']);

        return $person;
    }
}