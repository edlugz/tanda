<?php

namespace EdLugz\Tanda\Helpers;

class TandaHelper
{
    /**
     * get service provider from mobile number.
     *
     * @param string $mobileNumber - 0XXXXXXXXX
     *
     * @return string
     */
    public static function serviceProvider(string $mobileNumber): string
    {
        $safaricom = '/(?:0)?((?:(?:7(?:(?:[01249][0-9])|(?:5[789])|(?:6[89])))|(?:1(?:[1][0-5])))[0-9]{6})$/';
        $airtel = '/(?:0)?((?:(?:7(?:(?:3[0-9])|(?:5[0-6])|(8[5-9])))|(?:1(?:[0][0-2])))[0-9]{6})$/';
        $telkom = '/(?:0)?(77[0-9][0-9]{6})/';
        $equitel = '/(?:0)?(76[3-6][0-9]{6})/';
        if (preg_match($safaricom, $mobileNumber)) {
            return 'MPESA';
        } elseif (preg_match($airtel, $mobileNumber)) {
            return 'AIRTELMONEY';
        } elseif (preg_match($telkom, $mobileNumber)) {
            return 'TKASH';
        } elseif (preg_match($equitel, $mobileNumber)) {
            return 'EQUITEL';
        } else {
            return '0';
        }
    }
}
