<?php

namespace Ongoo\Component\Form;

/**
 * Description of Form
 *
 * @author paul
 */
class Form extends Observable
{

    protected $fields;
    protected $changes = null;
    //-
    protected $hasSuccess = true;
    protected $hasWarning = false;
    protected $hasError = false;
    //-
    protected $parameters = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->fields = array();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * 
     * @param String $parameters
     * @return \Ongoo\Component\Form\Form
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameter($parameter, $defaultValue = null)
    {
        if (!array_key_exists($parameter, $this->parameters))
        {
            return $defaultValue;
        }
        return $this->parameters[$parameter];
    }

    /**
     * 
     * @param String $parameter
     * @param mixed $value
     * @return \Ongoo\Component\Form\Form
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;
        return $this;
    }

    protected function makeField($fieldName)
    {
        return new Field($fieldName);
    }
    
    /**
     * 
     * @param string $fieldName
     * @return \Ongoo\Component\Form\Field
     */
    public function addField($fieldName)
    {
        $field = $this->getField($fieldName);
        if (null === $field)
        {
            $field = $this->makeField($fieldName);
            $this->fields[$fieldName] = &$field;
        }
        $this->watch($field, null, $field->getName() . '-{event}');
        $this->watch($field, null, 'field-{event}');
        
        $self = &$this;
        $field->on('error', function($field, $error) use(&$self){
            $self->setError(true);
        });
        $field->on('warning', function($field, $warning) use(&$self){
            $self->setWarning(true);
        });
        return $field;
    }
    
    public function setError($boolean)
    {
        $this->hasError = $boolean;
        return $this;
    }
    
    public function setWarning($boolean)
    {
        $this->hasWarning = $boolean;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * 
     * @param string $fieldName
     * @return \Ongoo\Component\Form\Field
     */
    public function getField($fieldName)
    {
        if (!array_key_exists($fieldName, $this->fields))
        {
            return null;
        }
        return $this->fields[$fieldName];
    }

    /**
     * 
     * @param string $fieldName
     * @return \Ongoo\Component\Form\Field
     */
    public function removeField($fieldName)
    {
        $field = null;
        if (array_key_exists($fieldName, $this->fields))
        {
            $field = $this->fields[$fieldName];
            unset($this->fields[$fieldName]);
        }
        return $field;
    }

    /**
     * 
     * @param string $fieldName
     * @return mixed
     */
    public function getValue($fieldName)
    {
        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return null;
        }

        $value = $field->getValue();
        return $value instanceof Values\NotSetValue ? $value->get() : $value;
    }
    
    /**
     * 
     * @param string $fieldName
     * @return mixed
     */
    public function getValues()
    {
        $result = array();
        foreach( $this->getFields() as $field )
        {
            $value = $field->getValue();
            $result[$field->getName()] = $value instanceof Values\NotSetValue ? $value->get() : $value;
        }
        return $result;
    }

    /**
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function isValueSet($fieldName)
    {
        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return false;
        }

        return $field->isValueSet();
    }

    /**
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function hasSuccess($fieldName = null)
    {
        if (is_null($fieldName))
        {
            return !$this->hasError();
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return false;
        }

        return $field->hasSuccess();
    }

    /**
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function hasFeedback($fieldName = null)
    {
        if (is_null($fieldName))
        {
            return $this->hasError | $this->hasWarning;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return false;
        }

        return $field->hasFeedback();
    }

    /**
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function hasWarning($fieldName = null)
    {
        if (is_null($fieldName))
        {
            return $this->hasWarning;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return false;
        }

        return $field->hasWarning();
    }

    /**
     * 
     * @param string $fieldName
     * @return boolean
     */
    public function hasError($fieldName = null)
    {
        if (is_null($fieldName))
        {
            return $this->hasError;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return true;
        }

        return $field->hasError();
    }

    public function getWarningMessages($fieldName = null)
    {
        if (is_null($fieldName))
        {
            $array = array();
            foreach ($this->fields as $field)
            {
                if ($field->hasWarning())
                {
                    $array[$field->getName()] = array();
                    foreach ($field->getWarnings() as $warning)
                    {
                        $array[$field->getName()][] = $warning->getRaxMessage();
                    }
                }
            }

            return $array;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return array();
        }

        if ($field->hasWarning())
        {
            $array = array();
            foreach ($field->getWarnings() as $warning)
            {
                $array[] = $warning->getMessage();
            }

            return $array;
        }
        return array();
    }

    /**
     * 
     * @param string|Field $fieldName
     * @return array
     */
    public function getErrorMessages($fieldName = null)
    {
        if (is_null($fieldName))
        {
            $errors = array();
            foreach ($this->fields as $field)
            {
                if ($field->hasError())
                {
                    $errors[$field->getName()] = array();
                    foreach ($field->getErrors() as $error)
                    {
                        $errors[$field->getName()][] = $error->getMessage();
                    }
                }
            }

            return $errors;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return array();
        }

        if ($field->hasError())
        {
            $errors = array();
            foreach ($field->getErrors() as $error)
            {
                $errors[] = $error->getMessage();
            }

            return $errors;
        }
        return array();
    }

    /**
     * 
     * @param string $fieldName
     * @return array
     */
    public function getErrors($fieldName = null)
    {
        if (is_null($fieldName))
        {
            $errors = array();
            foreach ($this->fields as $field)
            {
                if ($field->hasError())
                {
                    $errors[$field->getName()] = $field->getErrors();
                }
            }

            return $errors;
        }

        $field = $this->getField($fieldName);
        if (is_null($field))
        {
            return array();
        }

        if ($this->hasError($fieldName))
        {
            return $this->fields[$fieldName]->getErrors();
        }
        return array();
    }

    /**
     * 
     * @param string $fieldName
     * @return array
     */
    public function getWarnings($fieldName = null)
    {
        if (is_null($fieldName))
        {
            $warnings = array();
            foreach ($this->fields as $field)
            {
                if ($field->hasWarning())
                {
                    $warnings[$field->getName()] = $field->getWarnings();
                }
            }
            return $warnings;
        }

        if (!array_key_exists($fieldName, $this->fields))
        {
            return array();
        }

        if ($this->hasWarning($fieldName))
        {
            return $this->fields[$fieldName]->getWarnings();
        }
        return array();
    }

    /**
     * 
     * @param array $entity
     * @return \Ongoo\Component\Form\Form
     */
    public function initializeWithArray(array $entity)
    {
        foreach ($this->fields as $fieldName => $field)
        {
            if (array_key_exists($fieldName, $entity))
            {
                $field->initializeWith($entity[$fieldName]);
            }
        }
        return $this;
    }

    /**
     * 
     * @param array $form
     * @return array
     */
    public function fireBeforeValidateCallback(array $form)
    {
        return $form;
    }

    /**
     * 
     * @param array $form
     */
    public function fireAfterValidateCallback(array $form)
    {
        
    }

    /**
     * 
     * @param array $form
     * @return \Quartz\Component\FormValidator\FormValidator
     */
    public function validate(array $form)
    {
        $form = $this->fireBeforeValidateCallback($form);
        $this->hasFeedback = false;
        $this->setError(false);
        $this->setWarning(false);
        $this->changes = null;

        foreach ($this->fields as $fieldName => $field)
        {
            $value = array_key_exists($fieldName, $form) ? $form[$fieldName] : $field->getValue();
            $this->validateField($field, $value);
        }

        $this->fireAfterValidateCallback($form);
        return $this;
    }

    /**
     * 
     * @param mixed $fieldName
     * @param mixed $value
     * @throws \Exception
     */
    public function validateField($fieldName, $value)
    {
        if ($fieldName instanceof Field)
        {
            $field = $fieldName;
            $fieldName = $field->getName();
        } else
        {
            $field = $this->getField($fieldName);
        }

        if (!$field)
        {
            throw new \Exception('field [' . $fieldName . ']not found');
        }
        
        $field->validate($value);

        if ($field->hasError())
        {
            $this->hasError = true;
        }
        if ($field->hasWarning())
        {
            $this->hasWarning = true;
        }
        
        return $field->isValid();
    }

    public function getUpdatedFields()
    {
        $updatedFields = array();
        foreach ($this->fields as $fieldName => $field)
        {
            if ($field->hasChanged())
            {
                $updatedFields[$fieldName] = $field;
            }
        }
        return $updatedFields;
    }

    public function hasChanged()
    {
        foreach ($this->fields as $field)
        {
            if ($field->hasChanged())
            {
                return true;
            }
        }
        return false;
    }
    
}
