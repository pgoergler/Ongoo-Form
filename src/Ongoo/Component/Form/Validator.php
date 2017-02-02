<?php

namespace Ongoo\Component\Form;

/**
 * Description of Validator
 *
 * @author paul
 */
interface Validator
{

    public function validateValue(Field $field, $value);
    
    public function success(Field $field, $value);
    
    public function error(Field $field, $value, $message, $context = array());
    
    public function warning(Field $field, $value, $message, $context = array());
    
    public function onSuccess($callback);
    
    public function onError($callback);
    
    public function onWarning($callback);
}
