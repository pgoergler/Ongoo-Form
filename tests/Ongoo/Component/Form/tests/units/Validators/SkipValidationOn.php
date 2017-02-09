<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of SkipValidationOn
 *
 * @author paul
 */
class SkipValidationOn extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\SkipValidationOn('trigger');
        
        $this
            ->boolean($validator->validateValue($field, true))->isTrue()
            ->boolean($validator->validateValue($field, false))->isTrue()
            ->boolean($validator->validateValue($field, 'string'))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'trigger');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ForceValidationException')
                ->hasMessage("force pass")
            ;   
    }
}
