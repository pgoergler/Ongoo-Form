<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of CallbackValidator
 *
 * @author paul
 */
class CallbackValidator extends AbstractValidator
{
    protected $callback;
    
    public function __construct($function, $ifNotSet = false)
    {
        parent::__construct($ifNotSet);
        if(!is_callable($function) )
        {
            throw new \InvalidArgumentException('function is not callable');
        }
        $this->callback = $function;
    }
    
    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        $self = &$this;
        $proxy = function($method) use(&$self)
        {
            return function() use(&$self, $method){
                call_user_func_array(array($self, $method), func_get_args());
            };
        };
        
        $fn = $this->callback;
        $result = call_user_func_array($fn, [$field, $value, $proxy('error'), $proxy('warning'), $proxy('success')]);
        if( $result === null )
        {
            return $this->success($field, $value);
        }
        return $result;
    }
    
    public function validateValue(\Ongoo\Component\Form\Field $field, $value)
    {
        return $this->validateWithValue($field, $value);
    }
}
