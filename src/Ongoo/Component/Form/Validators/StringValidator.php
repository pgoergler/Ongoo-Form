<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of StringValidator
 *
 * @author paul
 */
class StringValidator extends LengthValidator
{
    public function __construct($minLength = 0, $maxLength = null, $tags = '', $ifNotSet = false)
    {
        parent::__construct($minLength, $maxLength, $tags, $ifNotSet);
    }
}
