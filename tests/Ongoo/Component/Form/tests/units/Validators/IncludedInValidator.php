<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of IncludedInValidator
 *
 * @author paul
 */
class IncludedInValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\IncludedInValidator(['v1', '3', 'true', 'false']);
        
        $this
            ->boolean($validator->validateValue($field, ['v1']))->isTrue()
            ->boolean($validator->validateValue($field, ['v1', '3']))->isTrue()
            ->boolean($validator->validateValue($field, ['false', 'v1', '3']))->isTrue()
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("NULL is not an array")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, ['v2']);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("[v2] is not included in [v1,3,true,false]")
      ;
    }
    
    public function testValidateValueWithStrict()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\IncludedInValidator(['v1', '3', 'true', 'false'], true);
        
        $this
            ->boolean($validator->validateValue($field, ['v1']))->isTrue()
            ->boolean($validator->validateValue($field, ['3', 'false']))->isTrue()
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, [3]);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("[3] is not included in [v1,3,true,false]")
            ;   
    }
}
