<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of RegexValidator
 *
 * @author paul
 */
class RegexValidator extends AbstractValidator
{

    protected $regex = null;
    protected $tags = null;

    public function __construct($regex, $tags = '', $ifNotSet = false)
    {
        $this->regex = $regex;
        $this->tags = $tags;
        parent::__construct($ifNotSet);
    }

    public function getRegex()
    {
        return '#' . str_replace('#', '\\#', $this->regex) . '#' . $this->tags;
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        
        if (is_null($value))
        {
            return true;
        }
        if (!is_string($value))
        {
            return $this->error($field, $value, "you must set a valid value");
            //throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "you must set a valid value");
        }

        $regex = $this->getRegex();
        if (!preg_match($regex, "$value", $m))
        {
            return $this->error($field, $value, "'{value}' does not match {1}", array('{1}' => $this->getRegex()));
            // throw new \Ongoo\Component\Form\Exceptions\ErrorException($field, $value, $value, "'{value}' does not match {1}", array('{1}' => $this->getRegex()));
        }
        return $this->success($field, $value);
    }

}
