<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of StringSanitizer
 *
 * @author paul
 */
class StringSanitizer extends CallbackSanitizer
{

    public function __construct()
    {
        parent::__construct(function($value)
        {
            if (is_object($value))
            {
                return $value->__toString();
            } elseif (is_string($value))
            {
                return $value;
            } elseif (is_array($value))
            {
                return implode(',', $value);
            }
            return "$value";
        });
    }

}
