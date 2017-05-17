<?php

namespace Ongoo\Component\Form\Validators;

/**
 * Description of DateTimeValidator
 *
 * @author paul
 */
class DateTimeValidator extends AbstractValidator
{

    protected $minimum = null;
    protected $maximum = null;
    protected $timezone = null;

    public function __construct($minimum = null, $maximum = null, $timezone = null)
    {
        parent::__construct();

        $this->timezone = $timezone ?: ($timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone(\date_default_timezone_get()));
        $this->minimum = $minimum ? \datetime($minimum, $timezone) : null;
        $this->maximum = $maximum ? \datetime($maximum, $timezone) : null;
    }

    public function sanitizeValue($value)
    {
        if ($value === null)
        {
            return $value;
        }
        if ($value instanceof \DateTime)
        {
            return $value;
        }

        try
        {
            return \datetime($value, $this->timezone);
        } catch (\Exception $ex)
        {
            return new \Quartz\Component\FormValidator\NotSetField($value);
        }

        return new \Quartz\Component\FormValidator\NotSetField($value);
    }

    protected function validateWithValue(\Ongoo\Component\Form\Field $field, $value)
    {
        if ($value instanceof \DateTime)
        {
            return $this->success($field, $value);
        }

        try
        {
            $datetime = \datetime($value, $this->timezone);
            if ($datetime)
            {
                return $this->success($field, $value);
            }
        } catch (\Exception $ex)
        {
            
        }
        return $this->error($field, $value, "'{value}' is not a valid datetime");
    }

}
