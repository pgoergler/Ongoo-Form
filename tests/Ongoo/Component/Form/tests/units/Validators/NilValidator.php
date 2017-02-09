<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of NilValidator
 *
 * @author paul
 */
class NilValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\NilValidator();
        
        $this
            ->boolean($validator->validateValue($field, true))->isTrue()
            ->boolean($validator->validateValue($field, false))->isTrue()
            ->boolean($validator->validateValue($field, 'true'))->isTrue()
            ->boolean($validator->validateValue($field, 'false'))->isTrue()
            ->boolean($validator->validateValue($field, 'yes'))->isTrue()
            ->boolean($validator->validateValue($field, 'no'))->isTrue()
            ->boolean($validator->validateValue($field, 'on'))->isTrue()
            ->boolean($validator->validateValue($field, 'off'))->isTrue()
            ->boolean($validator->validateValue($field, '1'))->isTrue()
            ->boolean($validator->validateValue($field, '0'))->isTrue()
            ->boolean($validator->validateValue($field, 1))->isTrue()
            ->boolean($validator->validateValue($field, 0))->isTrue()
            ->boolean($validator->validateValue($field, -1))->isTrue()
            ->boolean($validator->validateValue($field, new \stdClass))->isTrue()
            ;   
    }
}
