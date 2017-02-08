<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of BooleanValidator
 *
 * @author paul
 */
class BooleanValidator extends \Atoum\Helpers\Tester
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\FormField('test');
        $validator = new \Ongoo\Component\Form\Validators\BooleanValidator();
        
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
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("null is not valid") // null does not display
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \StdClass());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("object is not valid")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'Not a valid boolean');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("Not a valid boolean is not valid")
            ;   
    }
}
