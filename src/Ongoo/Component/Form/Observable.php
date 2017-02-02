<?php

namespace Ongoo\Component\Form;

/**
 * Description of Observable
 *
 * @author paul
 */
class Observable
{

    protected $observers = array();
    
    public function __construct()
    {
        $this->observers = array();
    }

    public function mergeEvents(Observable $other)
    {
        foreach( $other->observers as $callable)
        {
            $limit = $other->observers[$callable];
            $this->observers->attach($callable, $limit);
        }
    }
    
    public function watch(Observable $other, $remoteEvent, $localEvent = null)
    {
        $forward = is_null($localEvent) ? $remoteEvent : $localEvent;
        $self = &$this;
        $other->on($remoteEvent, function() use(&$self, $forward, $remoteEvent){
            
            $args = func_get_args();            
            if( is_null($remoteEvent) )
            {
                $remoteEvent = \array_shift($args);
            }
            
            if( is_null($forward) )
            {
                $forward = $remoteEvent;
            } else 
            {
                $forward = \str_replace('{event}', $remoteEvent, $forward);
            }
            
            
            \array_unshift($args, $forward);
            call_user_func_array(array($self, 'trigger'), $args);
        });
        return $this;
    }
    
    public function forwardTo(Observable $other, $localEvent, $remoteEvent = null)
    {
        return $other->watch($this, $localEvent, $remoteEvent);
    }
    
    public function one($event, callable $closure)
    {
        return $this->on($event, $closure, 1);
    }

    public function on($event, callable $closure, $limit = null)
    {
        if (!is_callable($closure))
        {
            throw new \InvalidArgumentException("must be a valid callable");
        }
        if (!isset($this->observers[$event]))
        {
            $this->observers[$event] = new \SplObjectStorage();
        }
        $this->observers[$event]->attach($closure, $limit);
        return $this;
    }

    public function off($event, callable $closure = null)
    {
        if( is_null($closure) )
        {
           if (isset($this->observers[$event]))
            {
                unset($this->observers);
            } 
        }
        else
        {        
            if (!is_callable($closure))
            {
                throw new \InvalidArgumentException("must be a valid callable");
            }

            if (isset($this->observers[$event]))
            {
                $this->observers[$event]->detach($closure);
            }
        }
        return $this;
    }

    /**
     * 
     * @param type $event
     * @return \SplObjectStorage
     */
    protected function getStorage($event)
    {
        if (!isset($this->observers[$event]))
        {
            return null;
        }
        return $this->observers[$event];
    }

    public function trigger($event)
    {
        $args = func_get_args();
        call_user_func_array(array($this, 'triggerEvent'), $args);
        
        \array_unshift($args, null);
        call_user_func_array(array($this, 'triggerEvent'), $args);
        return $this;
    }
    
    protected function triggerEvent($event)
    {
        $args = func_get_args();
        array_shift($args);

        $storage = $this->getStorage($event);
        if (!$storage)
        {
            return $this;
        }

        $remove = new \SplObjectStorage();
        foreach ($storage as $callable)
        {
            $limit = $storage[$callable];

            if ($callable instanceof \Closure)
            {
                $fn = $callable->bindTo($this);
            } else
            {
                $fn = $callable;
            }
            call_user_func_array($fn, $args);

            if (!is_null($limit))
            {
                $limit--;
                if ($limit > 0)
                {
                    $storage[$callable] = $limit;
                } else
                {
                    $remove[$callable] = true;
                }
            }
        }
        $storage->removeall($remove);

        return $this;
    }

}
