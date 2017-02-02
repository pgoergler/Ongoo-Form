<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of BooleanSanitizer
 *
 * @author paul
 */
class BooleanSanitizer extends CallbackSanitizer
{

    public function __construct()
    {
        parent::__construct(function($value)
        {
            if (is_bool($value))
            {
                return $value;
            } elseif (is_numeric($value))
            {
                return \boolval($value);
            } elseif (is_string($value))
            {
                if (in_array($value, array('yes', 'on', 'true', '1', 'YES', 'ON', 'TRUE')))
                {
                    return true;
                } elseif (in_array($value, array('no', 'off', 'false', '0', 'NO', 'OFF', 'FALSE')))
                {
                    return false;
                }
            }
            return false;
        });
    }

}
