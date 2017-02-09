<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of EnumValidator
 *
 * @author paul
 */
class EnumValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\EnumValidator(['v1', '3', 'true', 'false']);
        
        $this
            ->boolean($validator->validateValue($field, 'v1'))->isTrue()
            ->boolean($validator->validateValue($field, '3'))->isTrue()
            ->boolean($validator->validateValue($field, 3))->isTrue()
            ->boolean($validator->validateValue($field, 'true'))->isTrue()
            ->boolean($validator->validateValue($field, 'false'))->isTrue()
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, NULL not in v1,3,true,false")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \StdClass());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, stdClass not in v1,3,true,false")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'my_value');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, my_value not in v1,3,true,false")
            ;   
    }
    
    public function testValidateValueWithStrict()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\EnumValidator(['v1', '3', 'true', 'false'], true);
        
        $this
            ->boolean($validator->validateValue($field, 'v1'))->isTrue()
            ->boolean($validator->validateValue($field, '3'))->isTrue()
            ->boolean($validator->validateValue($field, 'true'))->isTrue()
            ->boolean($validator->validateValue($field, 'false'))->isTrue()
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 3);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, 3 not in v1,3,true,false")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, NULL not in v1,3,true,false")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \StdClass());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, stdClass not in v1,3,true,false")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'my_value');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must choose a valid value, my_value not in v1,3,true,false")
            ;   
    }
}
