<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of IncludedInValidator
 *
 * @author paul
 */
class IncludedInValidator extends EnumValidator
{

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
            return $this->error($field, $value, '[{0}] is not included in [{1}]', $e->getContext());
            //throw new \Ongoo\Component\Form\Exceptions\ErrorException($e->getField(), $e->getInitialValue(), $e->getValue(), '[{0}] is not included in [{1}]', $e->getContext());
        }
    }

}
