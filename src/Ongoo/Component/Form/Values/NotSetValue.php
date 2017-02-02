<?php

namespace Ongoo\Component\Form\Values;

/**
 * Description of NotSetValue
 *
 * @author paul
 */
class NotSetValue extends \Ongoo\Component\Form\Value
{

    public function __construct($value = null)
    {
        parent::__construct($value);
    }

    public function isValueSet()
    {
        return false;
    }

    public function __toString()
    {
        return "";
    }

}
