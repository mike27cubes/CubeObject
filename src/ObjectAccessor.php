<?php

namespace CubeObject;

trait ObjectAccessor
{
    public function __call($name, $args = array())
    {
        if (substr($name, 0, 3) == 'get') {
            return $this->magicGetter($name, $args);
        } elseif (substr($name, 0, 3) == 'has') {
            return $this->magicHas($name, $args);
        } elseif (substr($name, 0, 2) == 'is') {
            return $this->magicIs($name, $args);
        }
        throw new \BadMethodCallException('Invalid method "' . strip_tags($name) . '"');
    }

    protected function getPropertyName($name, $offset = 3)
    {
        $property = lcfirst(substr($name, $offset));
        if (property_exists($this, $property)) {
            return $property;
        }
        throw new \BadMethodCallException('Invalid Property "' . strip_tags($property) . '"');
    }

    protected function magicGetter($name, $args = array())
    {
        $property = $this->getPropertyName($name, 3);
        $lazyLoadMethod = 'lazyLoad' . ucfirst($property);
        if (method_exists($this, $lazyLoadMethod)) {
            call_user_func(array($this, $lazyLoadMethod));
        }
        return $this->$property;
    }

    protected function magicHas($name, $args = array())
    {
        $property = $this->getPropertyName($name, 3);
        return !empty($this->$property);
    }

    protected function magicIs($name, $args = array())
    {
        $property = $this->getPropertyName($name, 2);
        if (!is_bool($this->$property)) {
            throw new \BadMethodCallException('Invalid property check via "' . strip_tags($name) . '", "' . strip_tags($property) . '" is not a boolean value');
        }
        return $this->$property;
    }
}
