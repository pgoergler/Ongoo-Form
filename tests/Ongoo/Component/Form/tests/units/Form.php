<?php

namespace Ongoo\Component\Form\tests\units;

use \Ongoo\Component\Form\Validators\LengthValidator;

/**
 * Description of Form
 *
 * @author paul
 */
class Form extends \mageekguy\atoum\test
{

    public function testAddField()
    {
        $form = new \Ongoo\Component\Form\Form();
        $this
                ->if($field1 = $form->addField('field1'))
                ->then
                    ->object($field1)->isInstanceOf('\Ongoo\Component\Form\Field')
                    ->array($form->getFields())->hasSize(1)
                //-
                ->if($field10 = $form->addField('field1'))
                ->then
                    ->object($field10)->isInstanceOf('\Ongoo\Component\Form\Field')
                    ->array($form->getFields())->hasSize(1)
                    ->object($field10)->isIdenticalTo($field1)
                //-
                ->if($field2 = $form->addField('field2'))
                ->then
                    ->object($field2)->isInstanceOf('\Ongoo\Component\Form\Field')
                    ->array($form->getFields())->hasSize(2)
                //-
                ->if($field3 = $form->addField('field3'))
                ->then
                    ->object($field3)->isInstanceOf('\Ongoo\Component\Form\Field')
                    ->array($form->getFields())->hasSize(3)
                //-
                ->if($field11 = $form->getField('field1'))
                ->then
                    ->object($field11)->isIdenticalTo($field1)
                    ->array($form->getFields())->hasSize(3)
                //-
                ->if($fieldx = $form->getField('fieldx'))
                ->then
                    ->variable($fieldx)->isNull()
                    ->array($form->getFields())->hasSize(3)
                //-
                ->if($field21 = $form->getField('field2'))
                ->then
                    ->object($field21)->isIdenticalTo($field2)
                    ->array($form->getFields())->hasSize(3)
                //-
                ->if($field31 = $form->getField('field3'))
                ->then
                    ->object($field31)->isIdenticalTo($field3)
                    ->array($form->getFields())->hasSize(3)
                //-
                ->if($field22 = $form->removeField('field2'))
                ->then
                    ->object($field22)->isIdenticalTo($field2)
                    ->array($form->getFields())->hasSize(2)
                //-
                ->if($fieldx = $form->removeField('fieldx'))
                ->then
                    ->variable($fieldx)->isNull()
                    ->array($form->getFields())->hasSize(2)
        ;
    }

    /**
     * @tags toTest
     */
    public function testSanitizer()
    {
        $data = array(
            'field1' => 'new value'
        );

        $mock1 = new \mock\Ongoo\Component\Form\Form();
        $mock1->getMockController()->fireBeforeValidateCallback = function($form) {
            return $form;
        };
        $mock1->getMockController()->fireAfterValidateCallback = function($form) {

        };

        $mock1->addField('field1')
                ->addSanitizer(function($value) {
                    return "xx{$value}";
                })
                ->addSanitizer(function($value) {
                    return $value === 'inexistant' ? 'inexistant' : $value;
                })
                ->addSanitizer(function($value) {
                    return "{$value}xx";
                });

        $this->if($mock1->validate($data))
                ->then
                ->mock($mock1)->call('fireBeforeValidateCallback')
                    ->once()
                ->mock($mock1)->call('fireAfterValidateCallback')
                    ->once()
                ->mock($mock1)->call('validateField')
                //->withArguments('field1', "xx" . $data['field1'] . "xx")
                    ->withArguments('field1', $data['field1'])
                    ->once()
                ->string($mock1->getValue('field1'))->isEqualTo('xxnew valuexx')
        ;
    }

    public function testMissingValue()
    {
        $data = array();

        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field1')
                ->setDefaultAsNotSetValue();
        $form->addField('field2')
                ->setValue('missing value');

        $this->if($form->validate($data))
            ->then
                ->boolean($form->isValueSet('field1'))->isFalse()
                ->variable($form->getValue('field1'))->isNull()
                ->boolean($form->isValueSet('field2'))->isTrue()
                ->string($form->getValue('field2'))->isEqualTo('missing value')
        ;
    }

    public function testErrors()
    {
        $data = array();
        $form = new \Ongoo\Component\Form\Form();
        $form->addField('fieldExists');

        $this
                ->if($errors = $form->getErrors())
                ->then
                    ->array($errors)->isEmpty()
                ->if($errors = $form->getErrors('fieldExists'))
                ->then
                    ->array($errors)->isEmpty()
                ->if($errors = $form->getErrors('fieldNotExists'))
                ->then
                    ->array($errors)->isEmpty()
        ;

        $form->getField('fieldExists')->setMandatory(true);
        $form->validate($data);
        $this
                ->if($errors = $form->getErrors())
                ->then
                    ->array($errors)->hasSize(1)
                    ->array($errors)->hasKey('fieldExists')
                    ->array($fieldErrors = $errors['fieldExists'])->hasSize(1)

                    ->object($msg = $fieldErrors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getMessage())->isEqualTo('fieldExists is mandatory')
                    ->string($msg->getField()->getName())->isEqualTo('fieldExists')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('fieldExists')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->if($errors = $form->getErrors('fieldExists'))
                ->then
                    ->array($errors)->hasSize(1)
                    ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getMessage())->isEqualTo('fieldExists is mandatory')
                    ->string($msg->getField()->getName())->isEqualTo('fieldExists')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('fieldExists')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->if($errors = $form->getErrors('fieldNotExists'))
                ->then
                ->array($errors)->isEmpty()
        ;
    }

    public function testWarnings()
    {
        $data = array();
        $form = new \Ongoo\Component\Form\Form();
        $form->addField('fieldExists');

        $this
                ->if($array = $form->getWarnings())
                ->then
                    ->array($array)->isEmpty()
                ->if($array = $form->getWarnings('fieldExists'))
                ->then
                    ->array($array)->isEmpty()
                ->if($array = $form->getWarnings('fieldNotExists'))
                ->then
                    ->array($array)->isEmpty()
        ;

        $form->getField('fieldExists')->addValidator(function($field, $value) {
            throw new \Ongoo\Component\Form\Exceptions\WarningException($field, $value, $value, "warning message");
        });
        $form->validate($data);
        $this
                ->if($array = $form->getWarnings())
                ->then
                    ->array($array)->hasSize(1)
                    ->array($array)->hasKey('fieldExists')
                    ->array($fieldWarnings= $array['fieldExists'])->hasSize(1)
                
                    ->object($msg = $fieldWarnings[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                    ->string($msg->getMessage())->isEqualTo('warning message')
                    ->string($msg->getField()->getName())->isEqualTo('fieldExists')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('fieldExists')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->if($array = $form->getWarnings('fieldExists'))
                ->then
                    ->array($array)->hasSize(1)
                    ->object($msg = $array[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\WarningException')
                    ->string($msg->getMessage())->isEqualTo('warning message')
                    ->string($msg->getField()->getName())->isEqualTo('fieldExists')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('fieldExists')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->if($array = $form->getWarnings('fieldNotExists'))
                ->then
                    ->array($array)->isEmpty()
        ;
    }

    public function testMandatoryValue()
    {
        $data = array();

        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field1')
                ->setDefaultAsNotSetValue()
                ->setMandatory(true)
        ;
        $form->addField('field2')
                ->setDefaultAsNotSetValue()
                ->setMandatory(false)
        ;

        $this->if($form->validate($data))
                ->then
                    ->boolean($form->hasError())->isTrue()
                    ->array($form->getErrors())->isNotEmpty()
                    ->boolean($form->hasWarning())->isFalse()
                    ->array($form->getWarnings())->isEmpty()
                    ->boolean($form->hasSuccess())->isFalse()
                    //-
                    ->boolean($form->hasError('field1'))->isTrue()
                    ->array($errors = $form->getErrors('field1'))->hasSize(1)
                    //-
                    ->boolean($form->getField('field1')->hasError())->isTrue()
                    ->boolean($form->getField('field1')->hasWarning())->isFalse()
                    ->boolean($form->getField('field1')->hasSuccess())->isFalse()
                    //-
                ->if($fieldErrors = $form->getField('field1')->getErrors())
                ->then
                    ->array($fieldErrors)->hasSize(1)
                    ->object($msg = $fieldErrors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getMessage())->isEqualTo('field1 is mandatory')
                    ->string($msg->getField()->getName())->isEqualTo('field1')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('field1')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->and($errors = $form->getErrors('field1'))
                ->then
                    ->array($errors)->hasSize(1)
                    ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getMessage())->isEqualTo('field1 is mandatory')
                    ->string($msg->getField()->getName())->isEqualTo('field1')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('field1')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')

                    //-
                    ->boolean($form->hasSuccess('field2'))->isTrue()
                    ->boolean($form->hasWarning('field2'))->isFalse()
                    ->boolean($form->hasError('field2'))->isFalse()
        ;
    }

    public function testIntialValue()
    {
        $data = array();

        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field1')
                ->setDefaultAsNotSetValue()
        ;
        $form->addField('field2')
                ->setMandatory(true)
                ->setValue('intial value'); // setting value will not mark form has changed

        $this->if($form->validate($data))
                ->then
                ->boolean($form->hasChanged())->isFalse()
                ->boolean($form->isValueSet('field1'))->isFalse()
                ->variable($form->getValue('field1'))->isNull()
                ->boolean($form->isValueSet('field2'))->isTrue()
                ->string($form->getValue('field2'))->isEqualTo('intial value')
        ;
    }

    /*
    public function testCircularDependencies()
    {
        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field1')
                ->dependsOn('field2')
                ->setDefaultAsNotSetValue()
        ;
        $this->exception(function() use(&$form) {
            $form->addField('field2')
                    ->dependsOn('field1')
                    ->setDefaultValue('default value2')
            ;
        })->hasMessage('circular dependencies detected');
    }

    public function testDependsOn()
    {
        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field3')
                ->dependsOn('fields1', 'fields2')
                ->setDefaultValue('default value3')
                ->addSanitizer(function($value) {
                    return "xx{$value}";
                })
                ->addValidator(function($field, $value) use(&$form) {
                    if ($form->getValue('field1') != 'default value1')
                    {
                        throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, 'field1 value {value} is not equal to "default value1"');
                    }

                    if ($value === 'xxdefault value3')
                    {
                        return true;
                    }
                    throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not equal to "xxintial value3"');
                })
        ;
        $form->addField('field1')
                ->dependsOn('field2')
                ->setDefaultValue('default value1')
        ;
        $form->addField('field2')
                ->setDefaultValue('default value2')
        ;

        $data = array();
        $this->if($form->validate($data))
                ->then
                ->boolean($form->hasChanged())->isTrue()
                ->boolean($form->isValueSet('field1'))->isTrue()
                ->variable($form->getValue('field1'))->isEqualTo('default value1')
                ->boolean($form->isValueSet('field2'))->isTrue()
                ->string($form->getValue('field2'))->isEqualTo('default value2')
                ->boolean($form->isValueSet('field3'))->isTrue()
                ->string($form->getValue('field3'))->isEqualTo('xxintial value3')
        ;

        // TESTING FAIL field1
        $form = new \Ongoo\Component\Form\Form();
        $form->addField('field4')
                ->dependsOn('fields1', 'fields2')
                ->setDefaultValue('default value4')
                ->addValidator(function($field, $value) use(&$form) {
                    return $value === 'default value4';
                })
        ;
        $form->addField('field1')
                ->dependsOn('field2')
                ->setDefaultValue('intial value1')
                ->addValidator(new LengthValidator(1, 3))
        ;
        $form->addField('field2')
                ->setDefaultValue('default value2')
        ;

        $data = array('field1' => 'too long value');
        $this->if($form->validate($data))
                ->then
                ->boolean($form->hasChanged())->isTrue()
                ->boolean($form->isValueSet('field1'))->isFalse()
                ->variable($form->getValue('field1'))->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->array($errors = $form->getErrors('field1'))->hasSize(1)
                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getMessage())->isEqualTo('length must be between {0} and {1} characters')
                ->boolean($form->isValueSet('field2'))->isTrue()
                ->string($form->getValue('field2'))->isEqualTo('default value2')
                ->boolean($form->isValueSet('field4'))->isFalse()
                ->string($form->getValue('field4'))->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                ->array($errors = $form->getErrors('field4'))->hasSize(1)
                ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                ->string($msg->getMessage())->isEqualTo('could not validate {name} due to dependencies')
        ;
    }
    */
    public function testFull()
    {
        $data = array(
            'field1' => 'new value',
            'field3' => 'unexpected value'
        );

        $form = new \Ongoo\Component\Form\Form();
        $field1 = $form->addField('field1')
                ->setMandatory(true)
                ->setValue('missing value')
                ->addSanitizer(new \Ongoo\Component\Form\Sanitizers\TrimSanitizer())
                ->addSanitizer(function($value) {
                    return is_string($value) ? (\trim($value) ? "xx{$value}xx" : '') : $value;
                })
                ->addSanitizer(new \Ongoo\Component\Form\Sanitizers\EmptyAsSanitizer(new \Ongoo\Component\Form\Values\NotSetValue()))
                ->addValidator(function($field, $value) {
                    if (!is_string($value))
                    {
                        throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not a string');
                    }
                    return true;
                })
                ->addValidator(function($field, $value) {
            if ($value === 'xxnew valuexx')
            {
                return true;
            }
            throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not equal to "xxnew valuexx"');
        })
        ;

        $field2 = $form->addField('field2')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
                ->setSanitizers($field1->getSanitizers())
                ->setValidators($field1->getValidators())
        ;

        $field3 = $form->addField('field3')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
                ->setSanitizers($field1->getSanitizers())
                ->setValidators($field1->getValidators())
        ;
        $this
                ->if($form->validate($data))
                ->then
                    ->boolean($form->hasChanged())->isTrue()
                    ->array($form->getErrors('field1'))->isEmpty()
                    ->boolean($form->getField('field1')->hasSuccess())->isTrue()
                    ->string($form->getValue('field1'))->isEqualTo('xxnew valuexx')
                    //-
                    ->array($errors = $form->getErrors('field3'))->hasSize(1)
                    ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getRawMessage())->isEqualTo('{value} is not equal to "xxnew valuexx"')
                    ->string($msg->getField()->getName())->isEqualTo('field3')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('field3')
                    ->string($msg->getValue())->isEqualTo('xxunexpected valuexx')
                    ->string($msg->getInitialValue())->isEqualTo('unexpected value')
                    ->string($msg->getMessage())->isEqualTo($msg->getValue() . ' is not equal to "xxnew valuexx"')

                    //-
                    ->array($errors = $form->getErrors('field2'))->hasSize(1)
                    ->object($msg = $errors[0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($msg->getMessage())->isEqualTo('field2 is mandatory')
                    ->string($msg->getField()->getName())->isEqualTo('field2')
                    ->array($msg->getContext())->hasKey('{value}')
                    ->array($msg->getContext())->hasKey('{name}')
                    ->array($msg->getContext())->contains('field2')
                    ->object($msg->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                    ->object($msg->getInitialValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
        ;
    }

    public function testGetUpdatedFields()
    {
        $data = array(
            'field1' => 'new value',
        );

        $s1 = new \Ongoo\Component\Form\Sanitizers\TrimSanitizer();
        $s2 = function($value) {
            return is_string($value) ? (\trim($value) ? "xx{$value}xx" : '') : $value;
        };
        $s3 = new \Ongoo\Component\Form\Sanitizers\EmptyAsSanitizer(new \Ongoo\Component\Form\Values\NotSetValue());
        $v1 = function($field, $value) {
            if (!is_string($value))
            {
                throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not a string');
            }
            return true;
        };
        $v2 = function($field, $value) {
            if ($value === 'xxnew valuexx')
            {
                return true;
            }
            throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not equal to "xxnew valuexx"');
        };

        $form = new \Ongoo\Component\Form\Form();
        $field1 = $form->addField('field1')
                ->setMandatory(true)
                ->setValue('missing value')
                ->setSanitizers([$s1, $s2, $s3])
                ->setValidators([$v1, $v2])
        ;

        $field2 = $form->addField('field2')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
                ->setSanitizers([$s1, $s2, $s3])
                ->setValidators([$v1, $v2])
        ;

        $field3 = $form->addField('field3')
                ->setMandatory(true)
                ->setValue('intial value field 3')
                ->setSanitizers([$s1, $s2, $s3])
                ->setValidators([$v1, $v2])
        ;


        $this->boolean($form->hasChanged())->isFalse();

        $form->validate($data);

        $this->if($updated = $form->getUpdatedFields())
                ->boolean($form->hasChanged())->isTrue()
                ->array($updated)->hasSize(1)
                //-
                ->array($updated)->hasKey('field1')
                ->object($updated['field1'])->isIdenticalTo($field1)
                //-
                ->array($updated)->notHasKey('field3')
                //-
                ->array($updated)->notHasKey('field2')

        ;
    }

    public function testInitializeWithArray()
    {
        $data = array(
            'field1' => 'new value',
        );

        $form = new \Ongoo\Component\Form\Form();
        $field1 = $form->addField('field1')
                ->setMandatory(true)
                ->setValue('missing value')
        ;

        $field2 = $form->addField('field2')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
        ;

        $field3 = $form->addField('field3')
                ->setMandatory(true)
                ->setValue('intial value field 3')
        ;

        $form->initializeWithArray($data);

        $this->string($form->getValue('field1'))->isEqualTo('new value')
                ->boolean($form->isValueSet('field2'))->isFalse()
                ->string($form->getValue('field3'))->isEqualTo('intial value field 3')
        ;
    }

    public function testObservable()
    {
        $data = array(
            'field1' => 'new value',
            'field3' => 'unexpected value'
        );

        $form = new \Ongoo\Component\Form\Form();
        
        $events = array();
        $form->on(null, function($eventName, $field, $value = null) use(&$events){
            $fieldName = $field->getName();
            $events[$fieldName] = isset($events[$fieldName]) ? $events[$fieldName] : array();
            $events[$fieldName][$eventName] = isset($events[$fieldName][$eventName]) ? $events[$fieldName][$eventName] : array();
            $events[$fieldName][$eventName][] = isset($value) ? $value : null;
        });
        
        
        
        $field1 = $form->addField('field1')
                ->setMandatory(true)
                ->setValue('missing value')
                ->addSanitizer(new \Ongoo\Component\Form\Sanitizers\TrimSanitizer())
                ->addSanitizer(function($value) {
                    return is_string($value) ? (\trim($value) ? "xx{$value}xx" : '') : $value;
                })
                ->addSanitizer(new \Ongoo\Component\Form\Sanitizers\EmptyAsSanitizer(new \Ongoo\Component\Form\Values\NotSetValue()))
                ->addValidator(function($field, $value) {
                    if (!is_string($value))
                    {
                        throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not a string');
                    }
                    return true;
                })
                ->addValidator(function($field, $value) {
            if ($value === 'xxnew valuexx')
            {
                return true;
            }
            throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, '{value} is not equal to "xxnew valuexx"');
        })
        ;

        $field2 = $form->addField('field2')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
                ->setSanitizers($field1->getSanitizers())
                ->setValidators($field1->getValidators())
        ;

        $field3 = $form->addField('field3')
                ->setMandatory(true)
                ->setDefaultAsNotSetValue()
                ->setSanitizers($field1->getSanitizers())
                ->setValidators($field1->getValidators())
        ;
        $this
                ->if($form->validate($data))
                ->then
                    ->array($events)->hasSize(3)
                    ->array($events)->hasKey('field1')
                    ->array($events['field1'])->hasSize(4)
                    ->array($events['field1'])->hasKey('field1-success')
                    ->array($events['field1'])->hasKey('field1-validate')
                    ->array($events['field1'])->hasKey('field-success')
                    ->array($events['field1'])->hasKey('field-validate')
                
                    ->array($events)->hasKey('field2')
                    ->array($events['field2'])->hasSize(4)
                    ->array($events['field2'])->hasKey('field2-error')
                    ->array($events['field2'])->hasKey('field2-validate')
                    ->array($events['field2'])->hasKey('field-error')
                    ->array($events['field2'])->hasKey('field-validate')
                    ->array($events['field2']['field2-error'])->hasSize(1)
                    ->object($events['field2']['field2-error'][0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\MandatoryException')
                    ->object($events['field2']['field2-error'][0]->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                    ->array($events['field2']['field-error'])->hasSize(1)
                    ->object($events['field2']['field-error'][0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\MandatoryException')
                    ->object($events['field2']['field-error'][0]->getValue())->isInstanceOf('\Ongoo\Component\Form\Values\NotSetValue')
                
                    ->array($events)->hasKey('field3')
                    ->array($events['field3'])->hasSize(4)
                    ->array($events['field3'])->hasKey('field3-error')
                    ->array($events['field3'])->hasKey('field-error')
                    ->array($events['field3'])->hasKey('field3-validate')
                    ->array($events['field3'])->hasKey('field-validate')
                    ->array($events['field3']['field3-error'])->hasSize(1)
                    ->object($events['field3']['field3-error'][0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($events['field3']['field3-error'][0]->getValue())->isEqualTo('xxunexpected valuexx')
                    ->array($events['field3']['field-error'])->hasSize(1)
                    ->object($events['field3']['field-error'][0])->isInstanceOf('\Ongoo\Component\Form\Exceptions\ErrorException')
                    ->string($events['field3']['field-error'][0]->getValue())->isEqualTo('xxunexpected valuexx')
        ;
    }
}
