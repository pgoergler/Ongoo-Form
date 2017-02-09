<?php

namespace Ongoo\Component\Form\tests\units\Validators;

/**
 * Description of BooleanValidator
 *
 * @author paul
 */
class ArrayValidator extends \mageekguy\atoum\test
{
    public function testValidateValue()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $validator = new \Ongoo\Component\Form\Validators\ArrayValidator();
        
        eval('interface TestArray extends \ArrayAccess, \Traversable, \Countable {}');
        eval('interface TestArrayNotCountable extends \ArrayAccess, \Traversable {}');
        eval('interface TestArrayNotTraversable extends \ArrayAccess, \Countable {}');
        eval('interface TestArrayNotArrayAccess extends \Countable, \Traversable {}');
        
        $this
            //-
            ->boolean($validator->validateValue($field, []))->isTrue()
            ->boolean($validator->validateValue($field, new \mock\TestArray()))->isTrue()
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \mock\TestArrayNotCountable());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("mock\TestArrayNotCountable is not an array")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \mock\TestArrayNotTraversable());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("mock\TestArrayNotTraversable is not an array")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, new \mock\TestArrayNotArrayAccess());
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("mock\TestArrayNotArrayAccess is not an array")
            ->exception(function() use($validator, &$field) {
                    $validator->validateValue($field, null);
                })
                ->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->hasMessage("NULL is not an array")
            ;   
    }
}
