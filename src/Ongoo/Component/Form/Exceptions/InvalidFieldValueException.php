<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of InvalidFieldValueException
 *
 * @author paul
 */
abstract class InvalidFieldValueException extends \InvalidArgumentException
{
    public static function stringify($value)
    {
        if( \is_object($value) )
        {
            return ($value instanceof \Ongoo\Component\Form\Values\NotSetValue) ? '' : get_class($value);
        }
        elseif( \is_bool($value) )
        {
            return $value ? 'true' : 'false';
        }
        elseif( \is_string($value) || \is_numeric($value) )
        {
            return $value;
        }
        elseif( \is_array($value) )
        {
            return implode(', ', $value);
        }
        else 
        {
            return gettype($value);
        }
    }

    protected $name;
    protected $field;
    protected $value;
    protected $initialValue;
    protected $context;

    /**
     * 
     * @param \Ongoo\Component\Form\Field $field
     * @param mixed $initialValue
     * @param mixed $value
     * @param string $message
     * @param array $context
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct(\Ongoo\Component\Form\Field $field, $initialValue, $value, $message, $context = array(), $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->name = $field->getName();
        $this->field = $field;
        $this->rawMessage = $message;
        $this->initialValue = $initialValue;
        $this->value = $value;
        $this->context = array(
            '{raw_initial_value}' => $initialValue,
            '{raw_value}' => $value
        );

        if (!array_key_exists('{initial_value}', $context))
        {
            $this->setInitialValue($initialValue);
        }
        if (!array_key_exists('{value}', $context))
        {
            $this->setValue($value);
        }
        if (!array_key_exists('{name}', $context))
        {
            $this->context['{name}'] = $field->getName();
        }

        foreach ($context as $k => $v)
        {
            if (!preg_match('#^\{.*\}$#', $k))
            {
                $k = "{" . $k . "}";
            }
            $this->context[$k] = $v;
        }

        $this->message = $this->toString();
    }

    public function getField()
    {
        return $this->field;
    }

    public function setValue($value)
    {
        $this->context['{raw_value}'] = $value;
        $this->context['{value}'] = self::stringify($value);
        $this->value = $value;
        return $this;
    }

    public function setInitialValue($initialValue)
    {
        $this->context['{raw_initial_value}'] = $initialValue;
        $this->context['{initial_value}'] = self::stringify($initialValue);
        $this->initialValue = $initialValue;
        return $this;
    }

    public function getInitialValue()
    {
        return $this->initialValue;
    }

    public function getRawMessage()
    {
        return $this->rawMessage;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function toArray()
    {
        return array(
            'name' => $this->getName(),
            'field' => $this->getField(),
            'initial_value' => $this->getInitialValue(),
            'value' => $this->getValue(),
            'message' => $this->getRawMessage(),
        );
    }

    public function toString($forcedMessage = null) // implementation of getMessage()
    {
        $message = preg_replace('#\\\{#', '%accolate_open%', is_null($forcedMessage) ? $this->getMessage() : $forcedMessage);
        $message = preg_replace('#\\\}#', '%accolate_close%', $message);

        $context = $this->context;
        $context['%accolate_open%'] = '{';
        $context['%accolate_close%'] = '}';
        if (is_array($context['{value}']))
        {
            $context['{value}'] = implode(', ', $context['{value}']);
        }
        if (is_array($context['{initial_value}']))
        {
            $context['{initial_value}'] = implode(', ', $context['{initial_value}']);
        }
        return \strtr($message, $context);
    }

    public abstract function shouldStopValidation();
}
