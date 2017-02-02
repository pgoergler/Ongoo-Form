<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of UpperSanitizer
 *
 * @author paul
 */
class UpperSanitizer extends CallbackSanitizer
{

    public function __construct()
    {
        parent::__construct(function($value)
        {
            return \strtoupper("$value");
        });
    }

}
