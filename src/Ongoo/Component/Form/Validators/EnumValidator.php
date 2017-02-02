<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of EnumValidator
 *
 * @author paul
 */
class EnumValidator extends AbstractValidator
{

    protected $values = array();
    protected $strict = false;

    public function __construct($enumValues, $strict = false, $ifNotSet = false)
    {
        $this->values = $enumValues;
        $this->strict = $strict;
        parent::__construct($ifNotSet);
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if (!in_array($value, $this->values, $this->strict))
        {
            return $this->error($field, $value, "you must choose a valid value, {value} not in {values}", array('{values}' => implode(',', $this->values)));
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $field->getInitialValue(), $value, "you must choose a valid value, {value} not in {values}", array('{values}' => implode(',', $this->values)));
        }
        return $this->success($field, $value);
    }

}
