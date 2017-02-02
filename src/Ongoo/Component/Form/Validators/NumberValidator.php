<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of NumberSanitizer
 *
 * @author paul
 */
class NumberValidator extends RegexValidator
{

    protected $min = null;
    protected $max = null;
    protected $type = 'integer';
    protected $realType = 'integer';

    public function __construct($min = null, $max = null, $type = 'number', $ifNotSet = false)
    {
        if (func_num_args() >= 2)
        {
            $this->min = $min;
            $this->max = $max;
        } else if (func_num_args() == 1)
        {
            $this->max = $this->min = $min;
        } else if (func_num_args() == 0)
        {
            $this->min = null;
            $this->max = null;
        }

        switch ($type)
        {
            case 'unsigned float':
            case 'unsigned double':
                $regex = '^[0]*(?P<int>[1-9][0-9]*|0+)\.((?P<decimal>[0-9]*))?$';
                $this->realType = $type;
                $type = 'double';
                break;
            case 'unsigned integer':
                $regex = '^[0]*(?P<int>[1-9][0-9]*|0+)$';
                $this->realType = $type;
                $type = 'integer';
                break;
            case 'float':
            case 'double':
                $regex = '^(?P<sign>\-?)[0]*(?P<int>[1-9][0-9]*|0+)\.((?P<decimal>[0-9]*))?$';
                $this->realType = $type;
                break;
            case 'integer':
                $regex = '^(?P<sign>\-?)[0]*(?P<int>[1-9][0-9]*|0+)$';
                $this->realType = 'integer';
                break;
            case 'number':
            default:
                $regex = '^(?P<sign>\-?)[0]*(?P<int>[1-9][0-9]*|0+)(\.(?P<decimal>[0-9]*))?$';
                $this->realType = 'number';
                $type = 'float';
        }

        $this->type = $type;
        parent::__construct($regex, '', $ifNotSet);
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if (is_null($value))
        {
            return $this->success($field, $value);
        }

        try
        {
            parent::validateWithValue($field, "$value");
        } catch (\Ongoo\Component\Form\Exceptions\ErrorException $e)
        {
            return $this->error($field, $value, 'you must set a valid ' . $this->realType, $e->getContext());
            //throw new \Ongoo\Component\Form\Exceptions\ErrorException($e->getField(), $e->getInitialValue(), $e->getValue(), 'you must set a valid ' . $this->realType, $e->getContext());
        }

        settype($value, $this->type);

        if (!is_null($this->min) && $this->min > $value)
        {
            return $this->error($field, $value, 'you must a number greater than {0}', array('{0}' => $this->min));
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, 'you must a number greater than {0}', array('{0}' => $this->min));
        }

        if (!is_null($this->max) && $this->max < $value)
        {
            return $this->error($field, $value, 'you must a number lower than {0}', array('{0}' => $this->max));
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, 'you must a number lower than {0}', array('{0}' => $this->max));
        }
        
        return $this->success($field, $value);
    }

}
