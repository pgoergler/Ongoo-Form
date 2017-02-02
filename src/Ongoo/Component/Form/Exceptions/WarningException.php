<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of WarningException
 *
 * @author paul
 */
class WarningException extends InvalidFieldValueException
{

    public function shouldStopValidation()
    {
        return false;
    }

}
