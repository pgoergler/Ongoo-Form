<?php

namespace Ongoo\Component\Form;

/**
 * Description of CallbackForm
 *
 * @author paul
 */
class CallbackForm extends Form
{

    protected $beforeValidateCallback = null;
    protected $afterValidateCallback = null;
    protected $beforeBindCallback = null;
    protected $afterBindCallback = null;

    public function getBeforeValidateCallback()
    {
        return $this->beforeValidateCallback;
    }

    public function getAfterValidateCallback()
    {
        return $this->afterValidateCallback;
    }

    public function getBeforeBindCallback()
    {
        return $this->beforeBindCallback;
    }

    public function getAfterBindCallback()
    {
        return $this->afterBindCallback;
    }

    public function setBeforeValidateCallback($beforeValidateCallback)
    {
        if (!($beforeValidateCallback instanceof \Closure ))
        {
            throw new \InvalidArgumentException("must be a valid closure");
        }
        $this->beforeValidateCallback = $beforeValidateCallback;
    }

    public function setAfterValidateCallback($afterValidateCallback)
    {
        if (!($afterValidateCallback instanceof \Closure ))
        {
            throw new \InvalidArgumentException("must be a valid closure");
        }
        $this->afterValidateCallback = $afterValidateCallback;
    }

    public function setBeforeBindCallback($beforeBindCallback)
    {
        if (!($beforeBindCallback instanceof \Closure ))
        {
            throw new \InvalidArgumentException("must be a valid closure");
        }
        $this->beforeBindCallback = $beforeBindCallback;
    }

    public function setAfterBindCallback($afterBindCallback)
    {
        if (!($afterBindCallback instanceof \Closure ))
        {
            throw new \InvalidArgumentException("must be a valid closure");
        }
        $this->afterBindCallback = $afterBindCallback;
    }

    protected function execute($callback, $parameter)
    {
        if (is_callable($callback))
        {
            $args = func_get_args();
            array_shift($args);

            if ($callback instanceof \Closure)
            {
                $fn = $callback->bindTo($this);
            }
            return call_user_func_array($fn, $args);
        }
        return $parameter;
    }

    public function fireBeforeValidateCallback(array $form)
    {
        return $this->execute($this->getBeforeValidateCallback(), $form);
    }

    public function fireAfterValidateCallback(array $form)
    {
        $this->execute($this->getAfterValidateCallback(), $form);
    }

    public function fireBeforeBindCallback(\Quartz\Object\Entity &$object)
    {
        $this->execute($this->getBeforeBindCallback(), $object);
    }

    public function fireAfterBindCallback(\Quartz\Object\Entity &$object)
    {
        return $this->execute($this->getAfterBindCallback(), $object);
    }

}
