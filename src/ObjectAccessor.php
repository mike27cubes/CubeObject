<?php

/**
 * ObjectAccessor trait
 *
 * PHP version 5.4
 *
 * @vendor     27 Cubes
 * @package    CubeObject
 * @author     27 Cubes <info@27cubes.net>
 * @license    http://www.wtfpl.net WTFPL
 * @link       https://github.com/mike27cubes/CubeObject
 * @since      0.1.0
 */

namespace CubeObject;

/**
 * ObjectAccessor trait
 *
 * @vendor     27 Cubes
 * @package    CubeObject
 * @license    http://www.wtfpl.net WTFPL
 * @link       https://github.com/mike27cubes/CubeObject
 * @since      0.1.0
 */
trait ObjectAccessor
{
    /**
     * Magic Method Caller
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed
     * @throws \BadMethodCallException
     */
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

    /**
     * Gets the property name for the given method name and offset
     *
     * @param  string $methodName
     * @param  int    $offset
     * @return string property name
     * @throw  \BadMethodCallException
     */
    protected function getPropertyName($methodName, $offset = 3)
    {
        $property = lcfirst(substr($methodName, (int) $offset));
        if (property_exists($this, $property)) {
            return $property;
        }
        throw new \BadMethodCallException('Invalid Property "' . strip_tags($property) . '"');
    }

    /**
     * Magic Method: Getter
     *
     * Used by getProperty() method calls.
     *
     * @param  string $name
     * @param  array  $args
     * @return mixed  property contents
     */
    protected function magicGetter($name, $args = array())
    {
        $property = $this->getPropertyName($name, 3);
        $lazyLoadMethod = 'lazyLoad' . ucfirst($property);
        if (method_exists($this, $lazyLoadMethod)) {
            call_user_func(array($this, $lazyLoadMethod));
        }
        return $this->$property;
    }

    /**
     * Magic Method: Has
     *
     * Used by hasProperty() method calls.
     *
     * @param  string $name
     * @param  array  $args
     * @return bool   whether property has a non-empty value
     */
    protected function magicHas($name, $args = array())
    {
        $property = $this->getPropertyName($name, 3);
        return !empty($this->$property);
    }

    /**
     * Magic Method: Is
     *
     * Used by isProperty() method calls. The purpose is to determine if a
     * boolean flag is set to true. Examples of this would be methods like
     * isActive(), isEnabled(), isBlocked()
     *
     * @param  string $name
     * @param  array  $args
     * @return bool   whether boolean property is true
     */
    protected function magicIs($name, $args = array())
    {
        $property = $this->getPropertyName($name, 2);
        if (!is_bool($this->$property)) {
            throw new \BadMethodCallException('Invalid property check via "' . strip_tags($name) . '", "' . strip_tags($property) . '" is not a boolean value');
        }
        return $this->$property;
    }
}
