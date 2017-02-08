<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of EmptyValidator
 *
 * @author paul
 */
class SkipValidationOn extends AbstractValidator
{

    protected $triggerValue;
    protected $strict;
    protected $type;

    public function __construct($trigger, $strict = true)
    {
        $this->triggerValue = $trigger;
        $this->strict = $strict;
        if (is_null($trigger))
        {
            $this->type = 'null';
        } else if (is_array($trigger))
        {
            $this->type = 'array';
        } else
        {
            $this->type = 'common';
        }
    }

    protected function compare($value)
    {
        switch ($this->type)
        {
            case 'null':
                return \is_null($value);
            case 'common':
                if ($this->strict)
                {
                    return $this->triggerValue === $value;
                } else
                {
                    return $this->triggerValue == $value;
                }
            case 'array':
                if (!is_array($value))
                {
                    return false;
                }
                $diff1 = \array_diff($this->triggerValue, $value);
                if (!empty($diff1))
                {
                    return false;
                }
                $diff2 = \array_diff($value, $this->triggerValue);
                if (!empty($diff2))
                {
                    return false;
                }
        }
        return true;
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if ($this->compare($value))
        {
            return $this->error($field, $value, "force pass");
            // throw new \Ongoo\Component\Form\Exceptions\ForceValidationException($field, $field->getInitialValue(), $value, "force pass");
        }
        return $this->success($field, $value);
    }
    
    public function error(\Ongoo\Component\Form\Field $field, $value, $message, $context = array())
    {
        $this->trigger('error', $field, $value, $message, $context);
        throw new \Ongoo\Component\Form\Exceptions\ForceValidationException($field, $field->getInitialValue(), $value, "force pass");
    }

}
