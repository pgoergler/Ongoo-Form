<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of BooleanValidator
 *
 * @author paul
 */
class BooleanValidator extends AbstractValidator
{
    public function __construct($ifNotSet = false)
    {
        parent::__construct($ifNotSet);
    }
    
    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if (is_bool($value))
        {
            return $this->success($field, $value);
        }
        elseif (is_numeric($value))
        {
            return $this->success($field, $value);
        } elseif (is_string($value))
        {
            if( in_array($value, array('yes', 'on', 'true', '1', 'YES', 'ON', 'TRUE')) )
            {
                return $this->success($field, $value);
            }
            elseif( in_array($value, array('no', 'off', 'false', '0', 'NO', 'OFF', 'FALSE')) )
            {
                return $this->success($field, $value);
            }
        }
        
        if( is_null($value) )
        {
            $strValue = 'null';
        }
        else if(is_numeric($value) )
        {
            $strValue = $value;
        }
        else if( is_string($value) )
        {
            $strValue = $value;
        } else if(is_array($value))
        {
            $strValue = 'array';
        } else {
            $strValue = 'object';
        }
        
        return $this->error($field, $strValue, '{value} is not valid');
        // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "{value} is not valid");
    }

}
