<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of ForceValidation
 *
 * @author paul
 */
class ForceValidationException extends InvalidFieldValueException
{
    public function __construct(\Ongoo\Component\Form\Field $field, $initialValue, $value, $message, $context = array(), $code = 0, \Exception $previous = null)
    {
        parent::__construct($field, $initialValue, $value, $message, $context, $code, $previous);
    }
    
    public function shouldStopValidation()
    {
        return true;
    }

}
