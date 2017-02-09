<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of TextValidator
 *
 * @author paul
 */
class TextValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        
        $this
            ->if($validator = new \Ongoo\Component\Form\Validators\TextValidator(3, null))
            ->boolean($validator->validateValue($field, '123'))->isTrue()
            ->boolean($validator->validateValue($field, "12\n456"))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '12');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be greater than 3 characters")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be greater than 3 characters")
            ;

        $this
            ->if($validator = new \Ongoo\Component\Form\Validators\TextValidator(3, 5))
            ->boolean($validator->validateValue($field, '123'))->isTrue()
            ->boolean($validator->validateValue($field, "12\n45"))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '12');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be between 3 and 5 characters")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, "123\n56");
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be between 3 and 5 characters")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be between 3 and 5 characters")
            ;
        $this
            ->if($validator = new \Ongoo\Component\Form\Validators\TextValidator(3))
            ->boolean($validator->validateValue($field, '123'))->isTrue()
            //-
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '12');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be 3 characters")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, '123456');
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be 3 characters")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("length must be 3 characters")
            ;
    }
}
