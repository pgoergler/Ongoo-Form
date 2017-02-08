<?php

namespace Ongoo\Component\Form\tests\units;

use \Ongoo\Component\Form\Field as F;

/**
 * Description of Field
 *
 * @author paul
 */
class Field extends \mageekguy\atoum\test
{

    public function testConstructor()
    {
        {
            $field = new \Ongoo\Component\Form\Field();

            $this->variable($field->getName())->isNull();

            $this->boolean($field->isMandatory())->isFalse();
            $this->boolean($field->isValid())->isTrue();
            $this->boolean($field->isValueSet())->isFalse();

            $this->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue');
            $this->boolean($field->hasChanged())->isFalse();

            $this->boolean($field->hasSuccess())->isTrue();
            $this->boolean($field->hasError())->isFalse();
            $this->boolean($field->hasWarning())->isFalse();

            $this->array($field->getSanitizers())->isEmpty();
            $this->array($field->getValidators())->isEmpty();
        }

        {
            $field = new \Ongoo\Component\Form\Field('field1');

            $this->variable($field->getName())->isEqualTo('field1');

            $this->boolean($field->isMandatory())->isFalse();
            $this->boolean($field->isValid())->isTrue();
            $this->boolean($field->isValueSet())->isFalse();

            $this->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue');
            $this->boolean($field->hasChanged())->isFalse();

            $this->boolean($field->hasSuccess())->isTrue();
            $this->boolean($field->hasError())->isFalse();
            $this->boolean($field->hasWarning())->isFalse();

            $this->array($field->getSanitizers())->isEmpty();
            $this->array($field->getValidators())->isEmpty();
        }
    }

    public function testMandatory()
    {
        $field = new \Ongoo\Component\Form\Field();
        $this->boolean($field->isMandatory())->isFalse();

        $field->setMandatory(true);
        $this->boolean($field->isMandatory())->isTrue();

        $field->setMandatory(false);
        $this->boolean($field->isMandatory())->isFalse();
    }

    public function testName()
    {
        $field = new \Ongoo\Component\Form\Field();
        $this->variable($field->getName())->isNull();

        $field->setName("foo");
        $this->string($field->getName())->isEqualTo("foo");

        $field = new \Ongoo\Component\Form\Field("bar");
        $this->variable($field->getName())->isNotNull()
                ->string($field->getName())->isEqualTo("bar");

        $field->setName("foo");
        $this->string($field->getName())->isEqualTo("foo");
    }

    public function testSet()
    {
        $this->assert("Testing set");

        $field = new \Ongoo\Component\Form\Field('field1');
        $field->setValue('foo');

        $this->boolean($field->hasChanged())->isFalse();
        $this->boolean($field->isValid())->isTrue();
        $this->boolean($field->isValueSet())->isTrue();
        $this->string($field->getValue())->isEqualTo("foo");

        $field->setValue('bar');
        $this->boolean($field->hasChanged())->isTrue();
        $this->boolean($field->isValid())->isTrue();
        $this->boolean($field->isValueSet())->isTrue();
        $this->string($field->getValue())->isEqualTo("bar");

        $field->setValue(new \Ongoo\Component\Form\Values\NotSetValue());
        $this->boolean($field->hasChanged())->isTrue();
        $this->boolean($field->isValid())->isTrue();
        $this->boolean($field->isValueSet())->isFalse();
        $this->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue');
    }

    public function testDefaultValue()
    {
        $this->assert("Testing default value");

        {
            $field = new \Ongoo\Component\Form\Field('field1');
            $field->setValue("default value");
            $this->string($field->getValue())->isEqualTo("default value");
            $this->boolean($field->hasChanged())->isFalse();
            $this->boolean($field->isValueSet())->isTrue();
        }

        {
            $field = new \Ongoo\Component\Form\Field('field1');
            $field->setValue("initial value");
            $field->setValue("initial value");
            $this->string($field->getValue())->isEqualTo("initial value");
            $this->boolean($field->hasChanged())->isFalse();
            $this->boolean($field->isValueSet())->isTrue();
        }
    }

    public function testSanitizers()
    {
        $field = new \Ongoo\Component\Form\Field('field1');

        $this->array($field->getSanitizers())->isEmpty();
        $this->if($field->addSanitizer(function($value)
                {
                    return "xx{$value}";
                }))
        ->then->array($field->getSanitizers())->hasSize(1)
        ->then->if($field->addSanitizer(function($value)
                {
                    return "{$value}xx";
                }))
        ->then->array($field->getSanitizers())->hasSize(2);

        $this->if($sanitized = $field->sanitize('foo'))
                ->then
                ->string($sanitized)->isEqualTo('xxfooxx')
                ->boolean($field->isValid())->isTrue()
                ->boolean($field->isValueSet())->isFalse()
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
        $this->if($field->setSanitizers(array()))
                ->then
                ->array($field->getSanitizers())->isEmpty();
        $this->if($raw = $field->sanitize('foo'))
                ->then
                ->string($raw)->isEqualTo('foo');

        $this->if($field->addSanitizer(function($value)
                        {
                            return "xx{$value}";
                        }))
                ->and($field->addSanitizer(new \Ongoo\Component\Form\Sanitizers\TrimSanitizer()))
                ->and($field->addSanitizer(function($value)
                        {
                            return "{$value}yy";
                        }))
                ->then
                ->array($field->getSanitizers())->hasSize(3)
                ->string($field->sanitize('foo'))->isEqualTo('xxfooyy')
                ->string($field->sanitize(' foo    '))->isEqualTo('xx fooyy')
        ;

        $this->if($field->setSanitizers(array()))
                ->then
                ->array($field->getSanitizers())->isEmpty();

        $this
                ->if($field->addSanitizer(new \Ongoo\Component\Form\Sanitizers\TrimSanitizer()))
                ->and($field->addSanitizer(function($value)
                        {
                            return "xx{$value}";
                        }))
                ->and($field->addSanitizer(function($value)
                        {
                            return "{$value}yy";
                        }))
                ->then
                ->array($field->getSanitizers())->hasSize(3)
                ->string($field->sanitize('foo'))->isEqualTo('xxfooyy')
                ->string($field->sanitize(' foo    '))->isEqualTo('xxfooyy')
        ;
    }

    public function testValidators()
    {
        $mock1 = new \mock\Ongoo\Component\Form\Validator();
        $mock1->getMockController()->validateValue = function(\Ongoo\Component\Form\Field $field, $value){
            return true;
        };
        $mock2 = new \mock\Ongoo\Component\Form\Validator();
        $mock2->getMockController()->validateValue = function(\Ongoo\Component\Form\Field $field, $value){
            return true;
        };

        $field = new \Ongoo\Component\Form\Field('field1');
        $field->addValidator($mock1)
                ->addValidator($mock2)
        ;

        $this->if($validated = $field->validate('foo'))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->isValid())->isTrue()
                ->boolean($field->isValueSet())->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isFalse()
                //-
                ->string($field->getValue())->isEqualTo('foo')
                //-
                ->mock($mock1)->call('validateValue')
                        ->withArguments($field,'foo')
                        ->once()
                ->mock($mock2)->call('validateValue')
                        ->withArguments($field,'foo')
                        ->once()
        ;

        $this->given($this->resetMock($mock1))
            ->given($this->resetMock($mock2));

        $mock3 = new \mock\Ongoo\Component\Form\Validator();
        $mock3->getMockController()->validateValue = function(\Ongoo\Component\Form\Field $field, $value){
            throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "{value} is not valid");
        };

        $this->if($field->addValidator($mock3))
            ->and($validated = $field->validate('foo'))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                //-
                ->string($field->getValue())->isEqualTo('foo')
                //-
                ->mock($mock1)->call('validateValue')
                        ->withArguments($field,'foo')
                        ->once()
                ->mock($mock2)->call('validateValue')
                        ->withArguments($field,'foo')
                        ->once()
                ->mock($mock3)->call('validateValue')
                        ->withArguments($field,'foo')
                        ->once()

            ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)
                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{value} is not valid')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->contains('foo')
                ->string($msg->getField()->getName())->isEqualTo('field1')
                ->string($msg->getMessage())->isEqualTo($msg->getValue() . ' is not valid')
            ;
    }

    public function testMandatoryValue()
    {
        $field = new \Ongoo\Component\Form\Field('field1');
        $field->setMandatory(true);

        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->isValid())->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isTrue()
                //-
                ->variable($field->getValue())->isNull();

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)
                //-
                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('field1')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('field1')
                ->string($msg->getMessage())->isEqualTo('field1 is mandatory')
        ;

        $field->setSanitizers([function($value){
            // always return NotSetValue()
            return new \Ongoo\Component\Form\Values\NotSetValue($value);
        }]);

        $this
            ->if($validated = $field->validate('testMandatoryValue'))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                //-
                ->string($field->getValue())->isEqualTo('testMandatoryValue')
                //-
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('field1 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('field1')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('field1')
                ->array($msg->getContext())->contains('testMandatoryValue');

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('field1 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('field1')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('field1')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }

    public function testMandatoryDefaultValue()
    {
        $field = new \Ongoo\Component\Form\Field('mandatoryField_defaultFoo_1');
        $field->setMandatory(true)
                ->setValue('testMandatoryDefaultValue_1')
        ;
        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->isValid())->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isTrue()
                //-
                ->variable($field->getValue())->isNull();

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;

        $field = new \Ongoo\Component\Form\Field('mandatoryField_defaultFoo_3');
        $field->setMandatory(true)
                ->setValue('testMandatoryDefaultValue_3')
        ;
        $field->unsetValue();
        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->hasChanged())->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isTrue()
                //-
                ->variable($field->getValue())->isNull();

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('mandatoryField_defaultFoo_3 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('mandatoryField_defaultFoo_3')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('mandatoryField_defaultFoo_3')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;

        $field = new \Ongoo\Component\Form\Field('mandatoryField_defaultFoo_4');
        $field->setMandatory(true)
                ->unsetValue()
        ;
        $field->setSanitizers([function($value){
            // always return NotSetValue()
            return new \Ongoo\Component\Form\Values\NotSetValue($value);
        }]);

        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isFalse()
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('mandatoryField_defaultFoo_4 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('mandatoryField_defaultFoo_4')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('mandatoryField_defaultFoo_4')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue');

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('mandatoryField_defaultFoo_4 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('mandatoryField_defaultFoo_4')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('mandatoryField_defaultFoo_4')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }

    public function testHasChanged()
    {
        $field = new \Ongoo\Component\Form\Field('mandatoryField_defaultFoo_1');
        $field->setMandatory(true)
                ->setValue('testHasChanged')
                ->setValue("testHasChanged defined value")
        ;
        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isTrue()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->variable($field->getValue())->isNull();

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->isValid())->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;

        $field = new \Ongoo\Component\Form\Field('mandatoryField_defaultFoo_2');
        $field->setMandatory(true)
                ->unsetValue()
                ->setValue("defined value")
        ;
        $field->setSanitizers([function($value){
            // always return NotSetValue()
            return new \Ongoo\Component\Form\Values\NotSetValue($value);
        }]);

        $this
            ->if($validated = $field->validate(null))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('mandatoryField_defaultFoo_2 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('mandatoryField_defaultFoo_2')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('mandatoryField_defaultFoo_2')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
            ->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasChanged())->isFalse()
                //-
                ->and($errors = $field->getErrors())
                ->array($errors)->hasSize(1)

                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('{name} is mandatory')
                ->string($msg->getMessage())->isEqualTo('mandatoryField_defaultFoo_2 is mandatory')
                ->string($msg->getField()->getName())->isEqualTo('mandatoryField_defaultFoo_2')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('mandatoryField_defaultFoo_2')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }
    
    public function testWarnings()
    {
        $field = new \Ongoo\Component\Form\Field('testWarnings_1');
        $field->setValue("testWarnings initial value")
                ->addValidator(function($field, $value, $errorFn, $warningFn, $successFn) {
                    $warningFn($field, $value, "warning message");
                });

        $this->if($validated = $field->validate('testWarnings_value'))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isTrue()
                ->boolean($field->isValueSet())->isTrue()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->string($field->getValue())->isEqualTo('testWarnings_value')
                //-
                ->array($array = $field->getWarnings())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                ->string($msg->getMessage())->isEqualTo('warning message')
                ->string($msg->getField()->getName())->isEqualTo('testWarnings_1')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testWarnings_1')
                ->string($msg->getValue())->isEqualTo('testWarnings_value')
        ;
        
        $field = new \Ongoo\Component\Form\Field('testWarnings_2');
        $field->addValidator(function($field, $value, $errorFn, $warningFn, $successFn) {
                    $warningFn($field, $value, "warning message");
                });

        $this->if($validated = $field->validate('testWarnings_value2'))
            ->then
                ->boolean($validated)->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isTrue()
                ->boolean($field->isValueSet())->isTrue()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->string($field->getValue())->isEqualTo('testWarnings_value2')
                //-
                ->array($array = $field->getWarnings())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                ->string($msg->getMessage())->isEqualTo('warning message')
                ->string($msg->getField()->getName())->isEqualTo('testWarnings_2')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testWarnings_2')
                ->string($msg->getValue())->isEqualTo('testWarnings_value2')
        ;
        
        
        $field = new \Ongoo\Component\Form\Field('testWarnings_3');
        $field->addValidator(function($field, $value, $errorFn, $warningFn, $successFn) {
                    return $warningFn($field, $value, "warning message");
                });
        $field->addValidator(function($field, $value, $errorFn, $warningFn, $successFn) {
                    return $warningFn($field, $value, "warning message 2");
                });

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($field->isMandatory())->isFalse()
                ->boolean($validated)->isTrue()
                ->boolean($field->hasSuccess())->isTrue()
                ->boolean($field->hasError())->isFalse()
                ->boolean($field->hasWarning())->isTrue()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->array($array = $field->getWarnings())->hasSize(2)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                ->string($msg->getMessage())->isEqualTo('warning message')
                ->string($msg->getField()->getName())->isEqualTo('testWarnings_3')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testWarnings_3')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->object($msg = $array[1])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                ->string($msg->getMessage())->isEqualTo('warning message 2')
                ->string($msg->getField()->getName())->isEqualTo('testWarnings_3')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testWarnings_3')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }
    
    public function testErrors()
    {
        $field = new \Ongoo\Component\Form\Field('testErrors_1');
        $field->setValue("testErrors initial value")
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message1 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message2 {value} is not valid");
                });

        
        $this->if($validated = $field->validate('testErrors_value'))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->string($field->getValue())->isEqualTo('testErrors_value')
                //-
                ->array($array = $field->getErrors())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo('message1 {value} is not valid')
                ->string($msg->getMessage())->isEqualTo('message1 ' . $msg->getValue() . ' is not valid')
                ->string($msg->getField()->getName())->isEqualTo('testErrors_1')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testErrors_1')
                ->string($msg->getValue())->isEqualTo('testErrors_value')
        ;
        
        $field = new \Ongoo\Component\Form\Field('testErrors_2');
        $field->setDefaultAsNotSetValue()
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message3 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message4 {value} is not valid");
                });

        
        $this->if($validated = $field->validate('testErrors_value2'))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->string($field->getValue())->isEqualTo('testErrors_value2')
                //-
                ->array($array = $field->getErrors())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo("message3 {value} is not valid")
                ->string($msg->getMessage())->isEqualTo('message3 ' . $msg->getValue() . ' is not valid')
                ->string($msg->getField()->getName())->isEqualTo('testErrors_2')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testErrors_2')
                ->string($msg->getValue())->isEqualTo('testErrors_value2')
        ;
        
        $field = new \Ongoo\Component\Form\Field('testErrors_3');
        $field->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message5 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message6 {value} is not valid");
                });

        $this->if($validated = $field->validate('testErrors_value3'))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isTrue()
                //-
                ->string($field->getValue())->isEqualTo('testErrors_value3')
                //-
                ->array($array = $field->getErrors())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo("message5 {value} is not valid")
                ->string($msg->getMessage())->isEqualTo('message5 ' . $msg->getValue() . ' is not valid')
                ->string($msg->getField()->getName())->isEqualTo('testErrors_3')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testErrors_3')
                ->string($msg->getValue())->isEqualTo('testErrors_value3')
        ;
        
        
        $field = new \Ongoo\Component\Form\Field('testErrors_4');
        $field->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message7 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message8 {value} is not valid");
                });

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->array($array = $field->getErrors())->hasSize(1)
                ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getRawMessage())->isEqualTo("message7 {value} is not valid")
                ->string($msg->getMessage())->isEqualTo('message7 ' . $msg->getValue() . ' is not valid')
                ->string($msg->getField()->getName())->isEqualTo('testErrors_4')
                ->array($msg->getContext())->hasKey('{value}')
                ->array($msg->getContext())->hasKey('{name}')
                ->array($msg->getContext())->contains('testErrors_4')
                ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
        
        $field = new \Ongoo\Component\Form\Field('testErrors_5');
        $field->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\RecoverableErrorException($field, $value, $value, "message 5-1 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message 5-2 {value} is not valid");
                })
                ->addValidator(function($field, $value) {
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "message 5-3 is never shown");
                });

        $this->if($validated = $field->validate(new \Ongoo\Component\Form\Values\NotSetValue()))
            ->then
                ->boolean($validated)->isFalse()
                ->boolean($field->hasSuccess())->isFalse()
                ->boolean($field->hasError())->isTrue()
                ->boolean($field->hasWarning())->isFalse()
                ->boolean($field->isValueSet())->isFalse()
                ->boolean($field->hasChanged())->isFalse()
                //-
                ->object($field->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->array($array = $field->getErrors())->hasSize(2)
                ->object($msg1 = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg1->getRawMessage())->isEqualTo("message 5-1 {value} is not valid")
                ->string($msg1->getMessage())->isEqualTo('message 5-1 ' . $msg1->getValue() . ' is not valid')
                ->string($msg1->getField()->getName())->isEqualTo('testErrors_5')
                ->array($msg1->getContext())->hasKey('{value}')
                ->array($msg1->getContext())->hasKey('{name}')
                ->array($msg1->getContext())->contains('testErrors_5')
                ->object($msg1->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                //-
                ->object($msg2 = $array[1])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg2->getRawMessage())->isEqualTo("message 5-2 {value} is not valid")
                ->string($msg2->getMessage())->isEqualTo('message 5-2 ' . $msg2->getValue() . ' is not valid')
                ->string($msg2->getField()->getName())->isEqualTo('testErrors_5')
                ->array($msg2->getContext())->hasKey('{value}')
                ->array($msg2->getContext())->hasKey('{name}')
                ->array($msg2->getContext())->contains('testErrors_5')
                ->object($msg2->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }

    public function testForceValidation()
    {
        $field = new \Ongoo\Component\Form\Field('test');
        $field
                ->setValue('initial value')
                ->addValidator(new \Ongoo\Component\Form\Validators\SkipValidationOn('trigger'))
                ->addValidator(function($field, $value){
                    return false; // always reject validation
                });
                
        $this
                ->if($field->validate('trigger'))
                ->then
                ->boolean($field->isValid())->isTrue()
                ->string($field->getValue())->isEqualTo('trigger')
        ;
        $this
                ->if($field->validate('not trigger'))
                ->then
                ->boolean($field->isValid())->isFalse()
                ->string($field->getValue())->isEqualTo('not trigger')
        ;
        
        $field2 = new \Ongoo\Component\Form\Field('test');
        $field2
                ->setValue('initial value')
                ->addValidator(new \Ongoo\Component\Form\Validators\SkipValidationOn(null))
                ->addValidator(function($field, $value){
                    return false; // always reject validation
                });
        $this
                ->if($field2->validate(null))
                ->then
                ->boolean($field2->isValid())->isTrue()
                ->variable($field2->getValue())->isNull()
        ;
        $this
                ->if($field2->validate('not trigger'))
                ->then
                ->boolean($field2->isValid())->isFalse()
                ->string($field2->getValue())->isEqualTo('not trigger')
        ;
        
        $field3 = new \Ongoo\Component\Form\Field('test');
        $field3
                ->setValue('initial value')
                ->addValidator(new \Ongoo\Component\Form\Validators\SkipValidationOn([1, 2, "3"]))
                ->addValidator(function($field, $value){
                    return false; // always reject validation
                });
        $this
                ->if($field3->validate([1, 2, "3"]))
                ->then
                ->boolean($field3->isValid())->isTrue()
                ->array($field3->getValue())->hasSize(3)
                ->array($field3->getValue())->contains(1)
                ->array($field3->getValue())->contains(2)
                ->array($field3->getValue())->contains("3")
        ;
        $this
                ->if($field3->validate('not trigger'))
                ->then
                ->boolean($field3->isValid())->isFalse()
                ->string($field3->getValue())->isEqualTo('not trigger')
        ;
    }
    
    public function testCallback()
    {
        
        $validator1 = new \Ongoo\Component\Form\Validators\CallbackValidator(function(\Ongoo\Component\Form\Field $field, $value, $errorFn, $warningFn, $succesFn){
            return $succesFn($field, $value);
        });
        $validator1->onSuccess(function($field, $value){
            throw new \RuntimeException("success message");
        });
        
        $validator2 = new \Ongoo\Component\Form\Validators\CallbackValidator(function(\Ongoo\Component\Form\Field $field, $value, $errorFn, $warningFn, $succesFn){
            return $errorFn($field, $value, "error message");
        });
        $validator2->onError(function($field, $value, $message, $context){
            throw new \RuntimeException($message);
        });
        
        $validator3 = new \Ongoo\Component\Form\Validators\CallbackValidator(function(\Ongoo\Component\Form\Field $field, $value, $errorFn, $warningFn, $succesFn){
            return $warningFn($field, $value, "warning message");
        });
        $validator3->onWarning(function($field, $value, $message, $context){
            throw new \RuntimeException($message);
        });
        
        $validator4 = new \Ongoo\Component\Form\Validators\CallbackValidator(function(\Ongoo\Component\Form\Field $field, $value, $errorFn, $warningFn, $succesFn){
            return $errorFn($field, $value, "error message");
        });
        
        $validator4->onError(function($field, $value, $message, $context){
            throw new \RuntimeException($message . " default");
        });
        
        $field = new \Ongoo\Component\Form\Field('test');
        $field
                ->setValue('initial value')
                ->addValidator($validator1);
         
        $this
                ->exception(function() use(&$field){
                    $field->validate('some value');
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('success message')
                ->then
                ->boolean($field->isValid())->isTrue()
                ->string($field->getValue())->isEqualTo('some value')
        ;
                
        $field1 = new \Ongoo\Component\Form\Field('test');
        $field1
                ->setValue('initial value')
                ->addValidator($validator4, function($field, $value, $message, $context){
                    throw new \RuntimeException($message . " overrided");
                });
         
        $this
                ->exception(function() use(&$field1){
                    $field1->validate('some value');
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('error message overrided')
                ->then
                ->boolean($field1->isValid())->isTrue()
                ->string($field1->getValue())->isEqualTo('some value')
        ;
                
        $field2 = new \Ongoo\Component\Form\Field('test');
        $field2
                ->setValue('initial value')
                ->addValidator($validator2);
         
        $this
                ->exception(function() use(&$field2){
                    $field2->validate('some value');
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('error message')
                ->then
                ->boolean($field2->isValid())->isTrue()
                ->string($field2->getValue())->isEqualTo('some value')
        ;
                
        $field3 = new \Ongoo\Component\Form\Field('test');
        $field3
                ->setValue('initial value')
                ->addValidator($validator3);
         
        $this
                ->exception(function() use(&$field3){
                    $field3->validate('some value');
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('warning message')
                ->then
                ->boolean($field3->isValid())->isTrue()
                ->string($field3->getValue())->isEqualTo('some value')
        ;
                
        
        $field4 = new \Ongoo\Component\Form\Field('test');
        $field4
                ->setMandatory(true)
                ->setValue('initial value')
                ->addValidator($validator1)
                ->onMandatoryException(function($field, $value, $exception){
                    throw new \RuntimeException("mandatory message");
                });
         
        $this
                ->exception(function() use(&$field4){
                    $field4->validate(new \Ongoo\Component\Form\Values\NotSetValue());
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('mandatory message')
                ->then
                ->boolean($field4->isValid())->isTrue()
                ->object($field4->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
                
        /**
         * In onMandatoryException() we must set error to invalidate field
         */
        $field5 = new \Ongoo\Component\Form\Field('test');
        $field5
                ->setMandatory(true)
                ->setValue('initial value')
                ->addValidator($validator1)
                ->onMandatoryException(function($field, $value, $exception){
                    $field->addError($exception);
                    throw new \RuntimeException("mandatory message");
                });
         
        $this
                ->exception(function() use(&$field5){
                    $field5->validate(new \Ongoo\Component\Form\Values\NotSetValue());
                })
                ->isInstanceOf('\RuntimeException')
                ->hasMessage('mandatory message')
                ->then
                ->boolean($field5->isValid())->isFalse()
                ->object($field5->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
                
        
    }
}
