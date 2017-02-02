<?php

namespace Ongoo\Component\Form\Exceptions;

/**
 * Description of SanitizeException
 *
 * @author paul
 */
class SanitizeException extends \InvalidArgumentException
{

    protected $value;
    protected $context;

    public function __construct($value, $message, $context = array(), $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->value = $value;
        $this->context = array();
        $this->context['{value}'] = $value;
        
        foreach ($context as $k => $v)
        {
            if (is_numeric($k))
            {
                $k = "{" . $k . "}";
            }
            $this->context[$k] = $v;
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function shouldStopValidation()
    {
        return true;
    }

}
