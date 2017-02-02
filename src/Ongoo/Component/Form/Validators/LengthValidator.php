<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of LengthValidator
 *
 * @author paul
 */
class LengthValidator extends RegexValidator
{

    protected $minLength = 0;
    protected $maxLength = null;

    public function __construct($minLength = 0, $maxLength = null, $tags = '', $ifNotSet = false)
    {
        if (func_num_args() >= 2)
        {
            $this->minLength = $minLength;
            $this->maxLength = $maxLength;
        } else if (func_num_args() == 1)
        {
            $this->maxLength = $this->minLength = $minLength;
        } else if (func_num_args() == 0)
        {
            $this->minLength = 0;
            $this->maxLength = null;
        }
        parent::__construct(sprintf('^(.{%s,%s})$', $this->minLength, $this->maxLength), $tags, $ifNotSet);
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        try
        {
            return parent::validateWithValue($field, $value);
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $ex)
        {
            $message = $ex->getMessage();
            $context = $ex->getContext();
            $len = strlen($value);
            if ($this->maxLength === $this->minLength && $len != $this->minLength)
            {
                $message = 'length must be {0} characters';
                $context = array('{0}' => $this->minLength);
            } else if ($this->minLength < $this->maxLength && ($this->minLength > $len || $len > $this->maxLength))
            {
                $message = 'length must be between {0} and {1} characters';
                $context = array('{0}' => $this->minLength, '{1}' => $this->maxLength);
            } else if (is_null($this->maxLength) && $this->minLength > $len)
            {
                $message = 'length must be greater than {0} characters';
                $context = array('{0}' => $this->minLength);
            } else if (!is_null($this->maxLength) && $len > $this->maxLength )
            {
                $message = 'length must be lower than {0} characters';
                $context = array('{0}' => $this->maxLength);
            }
            else {
                $message = 'you must set a valid value';
            }

            return $this->error($field, $value, $message, $context);
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, $message, $context);
        }
    }

}
