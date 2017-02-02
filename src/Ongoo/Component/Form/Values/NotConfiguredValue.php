<?php

namespace Ongoo\Component\Form\Values;

/**
 * Description of NotConfiguredValue
 *
 * @author paul
 */
class NotConfiguredValue extends \Ongoo\Component\Form\Value
{

    public function __construct($value = null)
    {
        parent::__construct(null);
    }

    public function __toString()
    {
        return '';
    }

}
