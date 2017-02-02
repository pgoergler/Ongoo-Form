<?php

namespace Ongoo\Component\Form;

/**
 * Description of FormField
 *
 * @author paul
 */
class FormField extends Field
{

    //-
    protected $sanitizers = array();
    protected $validators = array();

    
    /**
     * 
     * @param array $sanitizors
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
    public function pushSanitizer($sanitizer)
    {
        if (!$this->validateSanitizer($sanitizer))
        {
            throw new \InvalidArgumentException('$sanitizer must be instance of \Ongoo\Component\Form\Sanitizer or callable');
        }
        array_push($this->sanitizers, $sanitizer);
        return $this;
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
            throw new \InvalidArgumentException('$sanitizer must be instance of \Ongoo\Component\Form\Sanitizer or callable');
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
    public function pushValidator($validator)
    {
        if (!$this->validateValidator($validator))
        {
            throw new \InvalidArgumentException('$validator must be instance of \Ongoo\Component\Form\Validator or callable');
        }
        array_push($this->validators, $validator);
        return $this;
    }

    /**
     * 
     * @param callable | \Ongoo\Component\Form\Validator $validator
     * @return \Ongoo\Component\Form\FormField
     */
    public function addValidator($validator)
    {
        if (!$this->validateValidator($validator))
        {
            throw new \InvalidArgumentException('$validator must be instance of \Ongoo\Component\Form\Validator or callable');
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

    public function validateWith($value, $validator)
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

    public function validate($value)
    {
        $this->reset();

        try
        {
            foreach ($this->sanitizers as $sanitizer)
            {
                $value = $this->sanitizeWith($value, $sanitizer);
            }
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $e)
        {
            $this->addError($e->getMessage(), $e->getContext());
            return false;
        }

        try
        {
            foreach ($this->validators as $validator)
            {
                try
                {
                    $result = $this->validateWith($value, $validator);
                    if ($result === true)
                    {
                        continue;
                    } elseif (is_string($result))
                    {
                        throw new \Ongoo\Component\Form\Exceptions\ErrorException($this, $value, $result);
                    } else
                    {
                        throw new \Ongoo\Component\Form\Exceptions\ErrorException($this, $value, "{value} is not valid");
                    }
                } catch (Ongoo\Component\Form\Exceptions\WarningException $ex)
                {
                    $this->addWarning($ex->getMessage(), $ex->getContext());
                }
            }
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $e)
        {
            $this->addError($e->getMessage(), $e->getContext());
            return false;
        }

        $this->setStatus(FieldStatus::SUCCESS);

        return true;
    }

}
