<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of TextValidator
 *
 * @author paul
 */
class TextValidator extends StringValidator
{

    public function __construct($minLength = 0, $maxLength = null, $ifNotSet = false)
    {
        if (func_num_args() == 1)
        {
            $maxLength = $minLength;
        }
        parent::__construct($minLength, $maxLength, 's', $ifNotSet);
    }

}
