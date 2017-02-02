<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of ArrayValidator
 *
 * @author paul
 */
class ArrayValidator extends AbstractValidator
{
    public function __construct($ifNotSet = false)
    {
        parent::__construct($ifNotSet);
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if (is_array($value) || ($value instanceof ArrayAccess && $value instanceof Traversable && $value instanceof Countable))
        {
            return $this->success($field, $value);
        }
        return $this->error($field, $value, '{value} is not an array');
        //throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not an array');
    }

}
