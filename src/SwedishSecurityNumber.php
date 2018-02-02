<?php namespace Proxify\ProxifyApi;

use Carbon\Carbon;
use DateTime;
use Exception;
use JsonSerializable;

/**
 * Extracts data, manipulates and validates swedish security numbers
 *
 * @link https://en.wikipedia.org/wiki/Personal_identity_number_(Sweden)
 */
class SwedishSecurityNumber implements JsonSerializable
{
    /**
     * @var string In format YYYYMMDD-XXXX
     */
    private $securityNumber = null;

    /**
     * SwedishSecurityNumber constructor.
     *
     * @param $securityNumber string
     * @return $this
     * @throws SwedishSocialSecurityNumberException
     */
    public static function id($securityNumber, $strict = true)
    {
        if ($strict && !self::isValid($securityNumber)) {
            throw new SwedishSocialSecurityNumberException('Invalid numbers');
        }


        $swedishSecurityNumber = new self;
        $swedishSecurityNumber->securityNumber = self::standardize($securityNumber);

        return $swedishSecurityNumber;
    }

    /**
     * Get number standardized
     *
     * @return string In format YYYYMMDD-XXXX
     */
    public function __toString() {
        return $this->securityNumber;
    }

    /**
     * Get json representation.
     *
     * @return string
     */
    public function jsonSerialize() {
        return $this->__toString();
    }

    /**
     * Get age
     * @return int Years old
     */
    public function age()
    {
        $year = (int) substr($this->securityNumber, 0, 4);
        $month = (int) substr($this->securityNumber, 4, 2);
        $day = (int) substr($this->securityNumber, 6, 2);

        return Carbon::createFromDate($year, $month, $day)->diffInYears();
    }

    /**
     * Get gender
     *
     * @return string|null Can be female|male or sometimes null if not strict
     */
    public function gender()
    {
        if (!self::isStandardizedFormat($this->securityNumber)) {
            return null;
        }

        $genderInt = substr($this->securityNumber, 11, 1);
        if ($genderInt % 2 == 0) {
            return 'female';
        }

        return 'male';
    }

    /**
     * Get number in format YYMMDDXXXX
     *
     * @return string YYMMDDXXXX
     */
    public function formatTenLetters()
    {
        return substr($this->formatTwelveLetters(), 2);
    }

    /**
     * Return the security number in YYYYMMDDXXXX
     *
     * @return string YYYYMMDDXXXX
     */
    public function formatTwelveLetters()
    {
        return str_replace('-', '', $this->securityNumber);
    }


    /**
     * Is the security number written in the format YYYYMMDD-XXXX
     *
     * @return boolean
     */
    public static function isStandardizedFormat($securityNumber)
    {
        return preg_match("/[0-9]{8}-[0-9]{4}/", $securityNumber) > 0;
    }

    /**
     * Validate Swedish personal identify number
     *
     * @param string $str
     * @return bool
     */
    public static function isValid($str)
    {
        $str = self::standardize($str);

        if (!self::isStandardizedFormat($str)) {
            return false;
        }

        $str = strval($str);
        $reg = '/^(\d{2}){0,1}(\d{2})(\d{2})(\d{2})([\-|\+]{0,1})?(\d{3})(\d{0,1})$/';
        preg_match($reg, $str, $match);
        if (!isset($match) || count($match) < 7) {
            return false;
        }
        $year    = $match[2];
        $month   = $match[3];
        $day     = $match[4];
        $num     = $match[6];
        $check   = $match[7];
        if (strlen($year) === 4) {
            $year = substr($year, 2);
        }
        $valid = self::luhn($year . $month . $day . $num) === intval($check);
        if ($valid && self::testDate($year, $month, $day)) {
            return $valid;
        }
        return $valid && self::testDate($year, $month, (intval($day) - 60));
    }

    /**
     * The Luhn algorithm
     *
     * @param string $str
     *
     * @return int
     */
    private static function luhn($str)
    {
        $sum = 0;
        for ($i = 0; $i < strlen($str); $i ++) {
            $v = intval($str[$i]);
            $v *= 2 - ($i % 2);
            if ($v > 9) {
                $v -= 9;
            }
            $sum += $v;
        }
        return intval(ceil($sum / 10) * 10 - $sum);
    }

    /**
     * Test date if luhn is true
     *
     * @param string|int $year Year
     * @param string|int $month Month
     * @param string|int $day Day
     *
     * @return bool
     */
    private static function testDate($year, $month, $day)
    {
        try {
            $date = new DateTime($year . '-' . $month . '-' . $day);
            if (strlen($month) < 2) {
                $month = '0' . $month;
            }
            if (strlen($day) < 2) {
                $day = '0' . $day;
            }
            return !(substr($date->format('Y'), 2) !== strval($year) ||
                $date->format('m') !== strval($month) ||
                $date->format('d') !== strval($day));
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Standardize
     *
     * @param $securityNumber string Different formats of the a swedish social security number
     * @return string YYYYMMDD-XXXX if it can
     * @throws SwedishSocialSecurityNumberException
     */
    private static function standardize($securityNumber){

        /* Remove all blank spaces*/
        $securityNumber = preg_replace('/\s+/', '', $securityNumber);

        /* YYYYMMDD-NNNN */
        if (preg_match("/[0-9]{8}-[0-9]{4}/", $securityNumber)) {
            return $securityNumber;
        }

        /* YYYYMMDDNNNN to YYYYMMDD-NNNN */
        if (preg_match("/[0-9]{12}/", $securityNumber)) {
            return substr($securityNumber, 0, 8) . '-' . substr($securityNumber, 8, 4);
        }

        /* YYMMDDNNNN to YYYYMMDD-NNNN */
        if (preg_match("/[0-9]{10}/", $securityNumber)) {
            $yy = (substr($securityNumber, 0, 2) <= date('y')) ? '20' : '19';
            return $yy . substr($securityNumber, 0, 6) . '-' . substr($securityNumber, 6, 4);
        }

        /* YYMMDDNNNN to YYYYMMDD-NNNN */
        if (preg_match("/[0-9]{6}-[0-9]{4}/", $securityNumber)) {
            $yy = (substr($securityNumber, 0, 2) <= date('y')) ? '20' : '19';
            return $yy . substr($securityNumber, 0, 6) . '-' . substr($securityNumber, 7, 4);
        }

        return $securityNumber;
    }


    /**
     * @param $id
     * @return bool
     */
    public static function valid($id){

        try {
            SwedishSecurityNumber::id($id);
        } catch (SwedishSocialSecurityNumberException $e) {
            return false;
        }

        return true;
    }
}
