<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of NilValidator
 *
 * @author paul
 */
class NilValidator extends AbstractValidator
{

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        return $this->success($field, $value);
    }

}