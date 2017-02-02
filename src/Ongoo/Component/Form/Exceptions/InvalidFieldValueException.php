<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of InvalidFieldValueException
 *
 * @author paul
 */
abstract class InvalidFieldValueException extends \InvalidArgumentException
{

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
        $this->context = array();

        if (!array_key_exists('{initial_value}', $context))
        {
            $this->context['{initial_value}'] = $initialValue;
        }
        if (!array_key_exists('{value}', $context))
        {
            $this->context['{value}'] = $value;
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

    public function setInitialValue($initialValue)
    {
        $this->context['{initial_value}'] = $initialValue;
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
        if(is_array($context['{value}']) )
        {
            $context['{value}'] = implode(', ', $context['{value}']);
        }
        if(is_array($context['{initial_value}']) )
        {
            $context['{initial_value}'] = implode(', ', $context['{initial_value}']);
        }
        return \strtr($message, $context);
    }

    public abstract function shouldStopValidation();

}
