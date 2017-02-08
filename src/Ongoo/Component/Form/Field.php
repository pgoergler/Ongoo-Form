<?php

namespace Ongoo\Component\Form;

use Ongoo\Component\Form\Values\NotSetValue,
    Ongoo\Component\Form\Values\NotConfiguredValue

;

/**
 * Description of Field
 *
 * @author paul
 */
class Field extends Observable
{

    protected $name;
    protected $status = FieldStatus::SUCCESS;
    protected $mandatory = false;
    //-
    protected $defaultValue = null;
    protected $value = null;
    protected $initialValue = null;
    //-
    protected $mandatoryExceptionHandler = null;
    protected $errors = array();
    protected $warnings = array();
    //-
    protected $sanitizers = array();
    protected $validators = array();

    public function __construct($name = null)
    {
        parent::__construct();
        $this->name = $name;
        //$this->value = new NotConfiguredValue();
        $this->value = new NotSetValue();
        $this->defaultValue = new NotSetValue();
        $this->initialValue = new NotConfiguredValue();
    }

    /**
     * 
     * @param string $name
     * @return \Ongoo\Component\Form\Field
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 
     * @param boolean $boolean
     * @return \Ongoo\Component\Form\Field
     */
    public function setMandatory($boolean)
    {
        $this->mandatory = $boolean;
        return $this;
    }

    /**
     * 
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }
    
    public function onMandatoryException($callback)
    {
        if( !is_callable($callback) )
        {
            throw new \InvalidArgumentException('must be callable');
        }
        $this->mandatoryExceptionHandler = $callback;
        return $this;
    }

    /**
     * Revert alias for hasError()
     * @return boolean
     */
    public function isValid()
    {
        return !$this->hasError();
    }

    /**
     * 
     * @return boolean
     */
    public function hasSuccess()
    {
        return $this->getStatus() === FieldStatus::SUCCESS || $this->getStatus() === FieldStatus::WARNING;
    }

    /**
     * 
     * @return boolean
     */
    public function hasError()
    {
        return !empty($this->errors);
    }

    /**
     * 
     * @return boolean
     */
    public function hasWarning()
    {
        return !empty($this->warnings);
    }

    /**
     * 
     * @return boolean
     */
    public function hasFeedback()
    {
        return $this->hasError() || $this->hasWarning();
    }

    public function hasChanged()
    {
        if (!$this->isInitialized())
        {
            return false;
        }

        $value = $this->getValue();
        $initialValue = (is_object($this->initialValue) ? get_class($this->initialValue) : "json" ) . ":" . json_encode($this->initialValue);
        $newValue = (is_object($value) ? get_class($value) : "json" ) . ":" . json_encode($value);
        return ($initialValue != $newValue);
    }

    public function reset()
    {
        $this->value = ($this->initialValue instanceof NotConfiguredValue) ? new NotSetValue() : $this->initialValue;
        $this->errors = array();
        $this->warnings = array();
        $this->status = FieldStatus::SUCCESS;
    }

    public function isInitialized()
    {
        return !($this->initialValue instanceof NotConfiguredValue);
    }

    public function initializeWith($value)
    {
        $this->initialValue = $value;
        $this->value = $value;
    }

    /**
     * 
     * @return mixed
     */
    public function getInitialValue()
    {
        return $this->initialValue;
    }

    /**
     * 
     * @return mixed
     */
    public function getValue()
    {
        /* if( $this->value instanceof NotConfiguredValue )
          {
          return $this->getDefaultValue();
          } */
        return $this->value;
    }

    /**
     * 
     * @param mixed $value
     * @return \Ongoo\Component\Form\Field
     */
    public function setValue($value)
    {
        $this->value = $value;
        if (!$this->isInitialized())
        {
            $this->initialValue = $value;
        }

        return $this;
    }

    public function isValueSet()
    {
        return !$this->hasError() && !($this->value instanceof NotSetValue) && !($this->value instanceof NotConfiguredValue);
    }

    /**
     * 
     * @return mixed
     */
    public function _getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * 
     * @param mixed $value
     * @return \Ongoo\Component\Form\Field
     */
    public function _setDefaultValue($value)
    {
        $this->defaultValue = $value;
        $this->setValue($value);
        return $this;
    }

    /**
     * 
     * @return \Ongoo\Component\Form\Field
     */
    public function setDefaultAsNotSetValue()
    {
        $this->initialValue = new Values\NotSetValue();
        return $this->setValue($this->initialValue);
    }

    /**
     * 
     * @return \Ongoo\Component\Form\Field
     */
    public function unsetValue()
    {
        return $this->setValue(new Values\NotSetValue());
    }

    /**
     * 
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status, $force = false)
    {
        if ($force)
        {
            $this->status = $status;
            return $this;
        }

        switch ($status)
        {
            case FieldStatus::ERROR:
                $this->status = $status;
                break;
            case FieldStatus::WARNING:
                if ($this->getStatus() === FieldStatus::ERROR)
                {
                    break;
                }
                $this->status = $status;
                break;
            case FieldStatus::SUCCESS:
            default:
                if ($this->getStatus() === FieldStatus::ERROR)
                {
                    break;
                }
                if ($this->getStatus() === FieldStatus::WARNING)
                {
                    break;
                }
                $this->status = $status;
                $this->trigger('success', $this);
        }
        return $this;
    }

    protected function buildContext(array $context)
    {
        $ctx = array();
        foreach ($context as $k => $v)
        {
            if (!preg_match('#^\{.*\}$#', $k))
            {
                $k = "{" . $k . "}";
            }
            $ctx[$k] = $v;
        }
        return $ctx;
    }

    /**
     * 
     * @param Exceptions\ErrorException $error
     * @return \Ongoo\Component\Form\Field
     */
    public function addError(Exceptions\ErrorException $error)
    {
        $this->errors[] = $error;
        $this->setStatus(FieldStatus::ERROR);
        $this->trigger('error', $this, $error);

        return $this;
    }

    public function getErrors()
    {
        return $this->hasError() ? $this->errors : array();
    }

    /**
     * 
     * @param Exceptions\WarningException $warning
     * @return \Ongoo\Component\Form\Field
     */
    public function addWarning(Exceptions\WarningException $warning)
    {
        $this->warnings[] = $warning;
        $this->setStatus(FieldStatus::WARNING);
        $this->trigger('warning', $this, $warning);
        return $this;
    }

    public function getWarnings()
    {
        return $this->hasWarning() ? $this->warnings : array();
    }

    /**
     * 
     * @param array $sanitizers
     * @return \Ongoo\Component\Form\FormField
     */
    public function setSanitizers(array $sanitizers)
    {
        $this->sanitizers = $sanitizers;
        return $this;
    }

    public function getSanitizers()
    {
        return $this->sanitizers;
    }

    protected function validateSanitizer($sanitizer)
    {
        if ($sanitizer instanceof Sanitizer)
        {
            return true;
        }

        return is_callable($sanitizer);
    }

    /**
     * 
     * @param callable | \Ongoo\Component\Form\Sanitizer $sanitizer
     * @return \Ongoo\Component\Form\FormField
     */
    public function addSanitizer($sanitizer)
    {
        if (!$this->validateSanitizer($sanitizer))
        {
            throw new \InvalidArgumentException('$sanitizer must be instance of \Ongoo\Component\Form\Sanitizer or callable ' . get_class($sanitizer) . ' given');
        }
        $this->sanitizers[] = $sanitizer;
        return $this;
    }

    protected function validateValidator($validator)
    {
        if ($validator instanceof Validator)
        {
            return true;
        }

        return is_callable($validator);
    }

    /**
     * 
     * @param type $validators
     * @return \Ongoo\Component\Form\FormField
     */
    public function setValidators(array $validators)
    {
        $this->validators = $validators;
        return $this;
    }

    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * 
     * @param callable | \Ongoo\Component\Form\Validator $validator
     * @return \Ongoo\Component\Form\FormField
     */
    public function addValidator($validator, $errorHandler = null, $warningHandler = null, $successHandler = null)
    {
        if (!$this->validateValidator($validator))
        {
            throw new \InvalidArgumentException('$validator must be instance of \Ongoo\Component\Form\Validator or callable ' . get_class($validator) . ' given');
        }
        
        if( !($validator instanceof Validator ))
        {
            $validator = new Validators\CallbackValidator($validator);
        }
        
        if( !is_null($errorHandler) )
        {
            $validator->off('error');
            $validator->onError($errorHandler);
        }
        if( !is_null($warningHandler) )
        {
            $validator->off('warning');
            $validator->onWarning($warningHandler);
        }
        if( !is_null($successHandler) )
        {
            $validator->off('success');
            $validator->onSuccess($successHandler);
        }
        $this->validators[] = $validator;
        return $this;
    }

    public function sanitizeWith($value, $sanitizer)
    {
        if ($sanitizer instanceof Sanitizer)
        {
            return $sanitizer->sanitizeValue($value);
        } else if (is_callable($sanitizer))
        {
            return $sanitizer($value);
        }
        throw new \InvalidArgumentException('$sanitizer must be instance of \Ongoo\Component\Form\Sanitizer or callable');
    }

    public function sanitize($value)
    {
        foreach ($this->sanitizers as $sanitizer)
        {
            $value = $this->sanitizeWith($value, $sanitizer);
        }
        return $value;
    }

    protected function executeValidator($value, $validator)
    {
        if ($validator instanceof Validator)
        {
            return $validator->validateValue($this, $value);
        } else if (is_callable($validator))
        {
            return $validator($this, $value);
        }
        throw new \InvalidArgumentException('$validator must be instance of \Ongoo\Component\Form\Validator or callable');
    }

    /**
     * 
     * @param mixed $value
     * @param callable|Validator $validator
     * @param boolean $throwErrorException
     * @param boolean $throwWarningException
     * @return boolean
     * @throws Exceptions\WarningException
     * @throws Exceptions\ErrorException
     */
    public function validateWith($value, $validator, $throwErrorException = false, $throwWarningException = false)
    {
        try
        {
            $result = $this->executeValidator($value, $validator);
            if ($result === true || is_null($result))
            {
                return $result;
            } elseif (is_string($result))
            {
                throw new Exceptions\ErrorException($this, $value, $value, $result);
            } else
            {
                throw new Exceptions\ErrorException($this, $value, $value, "{value} is not valid");
            }
        } catch (Exceptions\WarningException $ex)
        {
            if ($throwWarningException)
            {
                throw $ex;
            }
            $this->addWarning($ex);
        } catch (Exceptions\ErrorException $ex)
        {
            if ($throwErrorException)
            {
                throw $ex;
            }
            $this->addError($ex);
        }
    }

    public function validate($value)
    {
        try
        {
            $result = $this->validateImplementation($value);
            $this->trigger('validate', $this, $value);
            return $result;
        } catch(\Exception $e)
        {
            $this->trigger('validate', $this, $value);
            throw $e;
        }
    }
    
    protected function validateImplementation($value)
    {
        $this->reset();
        if (!$this->isInitialized())
        {
            $this->setDefaultAsNotSetValue();
        }
        $initialValue = $value;
        $this->setValue($value);

        $validatorIndex = null;
        try
        {
            foreach ($this->sanitizers as $validatorIndex => $sanitizer)
            {
                $value = $this->sanitizeWith($value, $sanitizer);
            }

            if ($value instanceof NotSetValue)
            {
                if ($this->isMandatory())
                {
                    $this->throwMandatoryException($initialValue, $value);
                }
            }
        } catch (Exceptions\ErrorException $e)
        {
            $e->setInitialValue($initialValue);
            $e->setName(is_null($validatorIndex) ? "no sanitizer" : "sanitizer #$validatorIndex");
            $this->addError($e);
            
            return false;
        }

        try
        {

            foreach ($this->validators as $validatorIndex => $validator)
            {
                try
                {
                    $this->validateWith($value, $validator, true, true);
                } catch (Exceptions\WarningException $ex)
                {
                    $ex->setInitialValue($initialValue);
                    $ex->setName("validator #$validatorIndex");
                    $this->addWarning($ex);
                } catch (Exceptions\ErrorException $ex)
                {
                    if ($ex->shouldStopValidation())
                    {
                        throw $ex;
                    } else
                    {
                        $ex->setInitialValue($initialValue);
                        $ex->setName("validator #$validatorIndex");
                        $this->addError($ex);
                    }
                }
            }
        } catch (Exceptions\ErrorException $e)
        {
            $e->setInitialValue($initialValue);
            $e->setName("validator #$validatorIndex");
            $this->addError($e);
            return false;
        } catch( Exceptions\ForceValidationException $ex ) {
        }

        $this->setStatus(FieldStatus::SUCCESS);
        $this->setValue($value);

        return true;
    }
    
    public function throwMandatoryException($initialValue, $value)
    {
        $e = new Exceptions\MandatoryException($this, $initialValue, $value, '{name} is mandatory');
        if( !is_null($this->mandatoryExceptionHandler))
        {
            return call_user_func($this->mandatoryExceptionHandler, $this, $value, $e);
        }
        throw $e;
    }

    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'initial' => $this->getInitialValue(),
                //'default' => $this->getDefaultValue()
        );
    }

    public function __toString()
    {
        return $this->getName();
    }

}
