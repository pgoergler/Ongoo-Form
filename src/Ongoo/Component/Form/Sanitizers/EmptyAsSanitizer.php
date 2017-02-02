<?php

namespace Ongoo\Component\Form\Sanitizers;

/**
 * Description of EmptyAsSanitizer
 *
 * @author paul
 */
class EmptyAsSanitizer extends AbstractSanitizer
{
    protected $defaultValue;
    
    public function __construct($defaultValue = null)
    {
        $this->defaultValue = $defaultValue;
    }
    
    public function sanitizeValue($value)
    {
        if (is_null($value) || $value === '' || (is_array($value) && empty($value)))
        {
            return $this->defaultValue;
        }
        return $value;
    }

}
