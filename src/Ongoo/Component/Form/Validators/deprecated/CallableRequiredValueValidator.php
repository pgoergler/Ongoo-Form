<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of CallableRequiredValueValidator
 *
 * @author paul
 */
class _CallableRequiredValueValidator extends AbstractRequiredValueValidator
{
    protected $callback;
    
    public function __construct($function)
    {
        $this->callback = $function;
    }
    
    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if(is_callable($this->callback) )
        {
            $fn = $this->callback;
            return call_user_func_array($fn, [$field, $value]);
        }
        throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, 'function is not a valid callable');
    }
}
