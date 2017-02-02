<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of LowerSanitizer
 *
 * @author paul
 */
class LowerSanitizer extends CallbackSanitizer
{

    public function __construct()
    {
        parent::__construct(function($value)
        {
            return \strtolower("$value");
        });
    }

}
