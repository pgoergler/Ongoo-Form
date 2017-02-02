<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of CallbackSanitizer
 *
 * @author paul
 */
class CallbackSanitizer implements \Ongoo\Component\Form\Sanitizer
{

    protected $func;

    public function __construct(callable $func = null)
    {
        $this->func = $func;
    }

    public function sanitizeValue($value)
    {
        $defaultFunc = function($value)
        {
            return $value;
        };
        $fn = is_null($this->func) ? $defaultFunc : $this->func;
        return $fn($value);
    }

}
