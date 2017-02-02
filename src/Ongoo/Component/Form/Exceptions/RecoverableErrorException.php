<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of ErrorException
 *
 * @author paul
 */
class RecoverableErrorException extends ErrorException
{

    public function shouldStopValidation()
    {
        return false;
    }

}
