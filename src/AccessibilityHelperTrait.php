<?php

namespace FriendsOfCake\TestUtilities;

use Exception;
use ReflectionProperty;
use ReflectionMethod;

/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Christian Winther, 2013
 */
trait AccessibilityHelperTrait
{
/**
 * Default target to use for reflection.
 *
 * @var Object|string
 */
    public $defaultReflectionTarget = null;

/**
 * List of Reflection properties made public.
 *
 * @var array
 */
    protected $_reflectionPropertyCache = [];

/**
 * List of Reflection methods made public.
 *
 * @var array
 */
    protected $_reflectionMethodCache = [];

/**
 * List of class names <=> instances used for invocation.
 *
 * @var array
 */
    protected $_reflectionInstanceCache = [];

/**
 * Reset the internal reflection caches.
 *
 * @return void
 */
    public function resetReflectionCache()
    {
        $this->_reflectionPropertyCache = [];
        $this->_reflectionMethodCache = [];
        $this->_reflectionInstanceCache = [];
    }

/**
 * Map an instance of an object to its class name.
 *
 * @param Object $instance
 * @return void
 */
    public function setReflectionClassInstance($instance, $class = null)
    {
        $class = $class ?: get_class($instance);
        $this->_reflectionInstanceCache[$class] = $instance;
    }

/**
 * Get working instance of "$class".
 *
 * @param string $class
 * @return Object
 * @throws \Exception
 */
    public function getReflectionInstance($class)
    {
        $class = $this->_getReflectionTargetClass($class);
        if (empty($this->_reflectionInstanceCache[$class])) {
            throw new Exception(sprintf('Unable to find instance of %s in the reflection cache. Have you added it using "setReflectionClassInstance"?', $class));
        }

        return $this->_reflectionInstanceCache[$class];
    }

/**
 * Helper method to call a protected method.
 *
 * @param string $method
 * @param array $args Argument list to call $method with (call_user_func_array style)
 * @param string $class Target reflection class
 * @return mixed
 */
    public function callProtectedMethod($method, $args = [], $class = null)
    {
        $class = $this->_getReflectionTargetClass($class);
        $cacheKey = $class . '_' . $method;

        if (!isset($this->_reflectionMethodCache[$cacheKey])) {
            $this->_reflectionMethodCache[$cacheKey] = $this->_getNewReflectionMethod($class, $method);
            $this->_reflectionMethodCache[$cacheKey]->setAccessible(true);
        }

        return $this->_reflectionMethodCache[$cacheKey]->invokeArgs($this->getReflectionInstance($class), $args);
    }

/**
 * Helper method to get the value of a protected property.
 *
 * @param string $property
 * @param string $class Target reflection class
 * @return mixed
 */
    public function getProtectedProperty($property, $class = null)
    {
        $Instance = $this->_getReflectionPropertyInstance($property, $class);
        return $Instance->getValue($this->getReflectionInstance($class));
    }

/**
 * Helper method to set the value of a protected property.
 *
 * @param string $property
 * @param mixed $value
 * @param string $class Target reflection class
 * @return mixed
 */
    public function setProtectedProperty($property, $value, $class = null)
    {
        $Instance = $this->_getReflectionPropertyInstance($property, $class);
        return $Instance->setValue($this->getReflectionInstance($class), $value);
    }

/**
 * Get a reflection property object.
 *
 * @param string $property
 * @param string $class
 * @return \ReflectionProperty
 */
    protected function _getReflectionPropertyInstance($property, $class)
    {
        $class = $this->_getReflectionTargetClass($class);
        $cacheKey = $class . '_' . $property;

        if (!isset($this->_reflectionPropertyCache[$cacheKey])) {
            $this->_reflectionPropertyCache[$cacheKey] = $this->_getNewReflectionProperty($class, $property);
            $this->_reflectionPropertyCache[$cacheKey]->setAccessible(true);
        }

        return $this->_reflectionPropertyCache[$cacheKey];
    }

/**
 * Get the reflection class name.
 *
 * @param string $class
 * @return string
 * @throws \Exception
 */
    protected function _getReflectionTargetClass($class)
    {
        $class = $class ?: $this->defaultReflectionTarget;

        if (!$class) {
            throw new Exception('Unable to find reflection target; have you set $defaultReflectionTarget or passed in a class name?');
        }

        if (!is_object($class)) {
            return $class;
        }

        return get_class($class);
    }

/**
 * Gets a new ReflectionMethod instance. Extracted for testing purposes.
 *
 * @param mixed $class
 * @param string $method
 * @return \ReflectionMethod
 */
    protected function _getNewReflectionMethod($class, $method)
    {
        return new ReflectionMethod($class, $method);
    }

/**
 * Gets a new ReflectionProperty instance. Extracted for testing purposes.
 *
 * @param mixed $class
 * @param string $property
 * @return \ReflectionProperty
 */
    protected function _getNewReflectionProperty($class, $property)
    {
        return new ReflectionProperty($class, $property);
    }
}
