<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of EmailValidator
 *
 * @author paul
 */
class EmailValidator extends RegexValidator
{

    public function __construct($tags = '', $ifNotSet = false)
    {
        parent::__construct('^((.*?@.*?(\.[a-z]+)+)?)$', $tags, $ifNotSet);
    }
    
    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        try
        {
            return parent::validateWithValue($field, $value);
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $e)
        {
            return $this->error($field, $value, 'you must set a valid email {value} not match {1}', $e->getContext());
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($e->getField(), $e->getInitialValue(), $e->getValue(), 'you must set a valid email {value} not match {1}', $e->getContext());
        }
    }

}
