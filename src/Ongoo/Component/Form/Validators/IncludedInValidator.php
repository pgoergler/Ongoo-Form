<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of IncludedInValidator
 *
 * @author paul
 */
class IncludedInValidator extends EnumValidator
{

    protected $arrayValidator;
    
    public function __construct($enumValues, $strict = false, $ifNotSet = false)
    {
        parent::__construct($enumValues, $strict, $ifNotSet);
        $this->arrayValidator = new ArrayValidator($ifNotSet);
    }
    
    public function validateValue(\Ongoo\Component\Form\Field $field, $value)
    {
        $this->arrayValidator->validateValue($field, $value);
        return parent::validateValue($field, $value);
    }
    
    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        try
        {
            foreach ($value as $v)
            {
                parent::validateWithValue($field, $v);
            }
            return $this->success($field, $value);
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $e)
        {
            return $this->error($field, $value, '[{value}] is not included in [{values}]', $e->getContext());
            //throw new \Ongoo\Component\Form\Exceptions\ErrorException($e->getField(), $e->getInitialValue(), $e->getValue(), '[{0}] is not included in [{1}]', $e->getContext());
        }
    }

}
