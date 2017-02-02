<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of AbstractValidator
 *
 * @author paul
 */
abstract class AbstractValidator extends \Ongoo\Component\Form\Observable implements \Ongoo\Component\Form\Validator
{

    protected $errorIfNotSetValue = false;
    protected $successCallback = null;
    protected $errorCallback = null;
    protected $warningCallback = null;

    public function __construct($errorIfNotSetValue = false)
    {
        parent::__construct();
        $this->errorIfNotSetValue = $errorIfNotSetValue;
    }

    protected abstract function validateWithValue(\Ongoo\Component\Form\Field $field, $value);

    protected function validateWithoutValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if ($this->errorIfNotSetValue)
        {
            return $this->error($field, $value, 'missing value');
        }
        return $this->success($field, $value);
    }

    public function validateValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if ($value instanceof \Ongoo\Component\Form\Values\NotSetValue)
        {
            return $this->validateWithoutValue($field, $value);
        }

        return $this->validateWithValue($field, $value);
    }

    public function success(\Ongoo\Component\Form\Field $field, $value)
    {
        $this->trigger('success', $field, $value);
        return true;
    }

    public function error(\Ongoo\Component\Form\Field $field, $value, $message, $context = array(), $code = 0)
    {
        $this->trigger('error', $field, $value, $message, $context);
        throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, null, $value, $message, $context, $code);
    }

    public function warning(\Ongoo\Component\Form\Field $field, $value, $message, $context = array(), $code = 0)
    {
        $this->trigger('warning', $field, $value, $message, $context);
        throw new \Ongoo\Component\Form\Exceptions\WarningException($field, null, $value, $message, $context, $code);
    }

    public function onSuccess($callback)
    {
        $this->on('success', $callback);
    }

    public function onError($callback)
    {
        $this->on('error', $callback);
    }

    public function onWarning($callback)
    {
        $this->on('warning', $callback);
    }

}
