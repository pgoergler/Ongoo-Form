<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of NumberValidator
 *
 * @author paul
 */
class NumberValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('fieldName');
        $validator = new \Ongoo\Component\Form\Validators\NumberValidator();
        
        $this
            ->boolean($validator->validateValue($field, '1234'))->isTrue()
            ->boolean($validator->validateValue($field, '-1.234'))->isTrue()
            ->boolean($validator->validateValue($field, 1234))->isTrue()
            ->boolean($validator->validateValue($field, -1.234))->isTrue()
            ->boolean($validator->validateValue($field, true))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, false);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'testString');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
        ;
    }
    
    public function testValidateValueMinValue()
    {
        $field = new \Ongoo\Component\Form\Field('fieldName');
        $validator = new \Ongoo\Component\Form\Validators\NumberValidator(3);
        
        $this
            ->boolean($validator->validateValue($field, '4'))->isTrue()
            ->boolean($validator->validateValue($field, 5))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '-1.234');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number greater than 3")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, false);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'testString');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 2);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number greater than 3")
        ;
    }
    
    public function testValidateValueMaxValue()
    {
        $field = new \Ongoo\Component\Form\Field('fieldName');
        $validator = new \Ongoo\Component\Form\Validators\NumberValidator(null, 3);
        
        $this
            ->boolean($validator->validateValue($field, '1'))->isTrue()
            ->boolean($validator->validateValue($field, '-1.234'))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '1234');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number lower than 3")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, false);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'testString');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 20);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number lower than 3")
        ;
    }
    
    public function testValidateValueMinMaxValue()
    {
        $field = new \Ongoo\Component\Form\Field('fieldName');
        $validator = new \Ongoo\Component\Form\Validators\NumberValidator(1, 3);
        
        $this
            ->boolean($validator->validateValue($field, '1'))->isTrue()
            ->boolean($validator->validateValue($field, 2))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '1234');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number lower than 3")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, false);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'testString');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid number")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '-1.234');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must a number greater than 1")
        ;
    }
}
