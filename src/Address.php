<?php namespace Proxify\ProxifyApi;

class Address
{
    /**
     * @var string Name
     */
    public $firstName;

    /**
     * @var string Last name
     */
    public $lastName;

    /**
     * @var string Care of
     */
    public $careOf;

    /**
     * @var string Street address line 1
     */
    public $line1;

    /**
     * @var string Street address line 2
     */
    public $line2;

    /**
     * @var string Postal code ex. 113 56
     */
    public $postalCode;

    /**
     * @var string Post city
     */
    public $postCity;

    /**
     * @var string Country
     */
    public $country = 'SE';

    /**
     * Get street address
     *
     * Line 1 and 2 merged
     *
     * @return  string
     */
    public function getStreetAddress()
    {
        return implode(' ', array_filter([$this->line1, $this->line2]));
    }

    /**
     * Print the address as HTML
     *
     * @return string HTML
     */
    public function getHtml()
    {
        return implode('<br>', $this->getLines());
    }

    /**
     * Get an array of address lines
     *
     * @return string[]
     */
    public function getLines()
    {
        $lines = [
            $this->firstName . ' ' . $this->lastName,
            $this->careOf,
            $this->line1,
            $this->line2,
            $this->postalCode . ' ' . $this->postCity,
        ];

        return array_filter($lines);

    }

    /**
     * Create from API
     *
     * @param $response array
     * @return Address
     */
    public static function createFromApiResponse($response)
    {
        $address = new self;
        $address->careOf = $response['careOf'];
        $address->line1 = $response['line1'];
        $address->line2 = $response['line2'];
        $address->postalCode = $response['postalCode'];
        $address->postCity = $response['postCity'];

        return $address;
    }
}
