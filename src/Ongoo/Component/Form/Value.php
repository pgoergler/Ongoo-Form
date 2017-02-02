<?php

namespace Ongoo\Component\Form;

/**
 * Description of Value
 *
 * @author paul
 */
class Value
{

    protected $value;

    public function __construct($value)
    {

        if ($value instanceof Value)
        {
            $this->value = $value->get();
        }
    }

    public function get()
    {
        return $this->value;
    }

    public function set($value)
    {
        $this->value = $value;
    }

    public function isValueSet()
    {
        return !($this->get() instanceof Values\NotSetValue);
    }

}
