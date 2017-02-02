<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of ErrorException
 *
 * @author paul
 */
class ErrorException extends InvalidFieldValueException
{

    public function shouldStopValidation()
    {
        return true;
    }

}
