<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of EmailValidator
 *
 * @author paul
 */
class EmailValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('fieldName');
        $validator = new \Ongoo\Component\Form\Validators\EmailValidator();
        
        $this
            ->boolean($validator->validateValue($field, 'foo@bar.com'))->isTrue()
            ->boolean($validator->validateValue($field, 'f@b.fr'))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, true);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid email 1 not match #^((.*?@.*?(\.[a-z]+)+)?)$#")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 'testString');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid email testString not match #^((.*?@.*?(\.[a-z]+)+)?)$#")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, 1);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid email 1 not match #^((.*?@.*?(\.[a-z]+)+)?)$#")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid email NULL not match #^((.*?@.*?(\.[a-z]+)+)?)$#")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \StdClass());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("you must set a valid email stdClass not match #^((.*?@.*?(\.[a-z]+)+)?)$#")
            ;   
    }
}
