<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities;

use Exception;
use ReflectionMethod;
use ReflectionProperty;

/**
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
     * @var object|string
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
    public function resetReflectionCache(): void
    {
        $this->_reflectionPropertyCache = [];
        $this->_reflectionMethodCache = [];
        $this->_reflectionInstanceCache = [];
    }

    /**
     * Map an instance of an object to its class name.
     *
     * @param  object $instance an instance of a class
     * @param  string|null $class the key used in the reflection instance class
     * @return void
     */
    public function setReflectionClassInstance(object $instance, ?string $class = null): void
    {
        $class = $class ?: get_class($instance);
        $this->_reflectionInstanceCache[$class] = $instance;
    }

    /**
     * Get working instance of "$class".
     *
     * @param  string|null $class the class name
     * @return object
     * @throws \Exception
     */
    public function getReflectionInstance(?string $class = null): object
    {
        $class = $this->_getReflectionTargetClass($class);
        if (empty($this->_reflectionInstanceCache[$class])) {
            throw new Exception(sprintf(
                'Unable to find instance of %s in the reflection cache. '
                . 'Have you added it using "setReflectionClassInstance"?',
                $class
            ));
        }

        return $this->_reflectionInstanceCache[$class];
    }

    /**
     * Helper method to call a protected method.
     *
     * @param  string $method the method name
     * @param  array  $args   Argument list to call $method with (call_user_func_array style)
     * @param  string $class  Target reflection class
     * @return mixed
     */
    public function callProtectedMethod(string $method, array $args = [], ?string $class = null)
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
     * @param  string $property the property to access/manipulate
     * @param  string $class    Target reflection class
     * @return mixed
     */
    public function getProtectedProperty(string $property, ?string $class = null)
    {
        $Instance = $this->_getReflectionPropertyInstance($property, $class);

        return $Instance->getValue($this->getReflectionInstance($class));
    }

    /**
     * Helper method to set the value of a protected property.
     *
     * @param  string $property the property to change
     * @param  mixed  $value the value to set the property to
     * @param  string $class    Target reflection class
     * @return mixed
     */
    public function setProtectedProperty(string $property, $value, ?string $class = null)
    {
        $Instance = $this->_getReflectionPropertyInstance($property, $class);

        return $Instance->setValue($this->getReflectionInstance($class), $value);
    }

    /**
     * Get a reflection property object.
     *
     * @param  string $property the property to access/manipulate
     * @param  string|null $class the class name
     * @return \ReflectionProperty
     */
    protected function _getReflectionPropertyInstance(string $property, ?string $class = null): ReflectionProperty
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
     * @param  string|object|null $class the class name
     * @return string
     * @throws \Exception
     */
    protected function _getReflectionTargetClass($class = null): string
    {
        $class = $class ?: $this->defaultReflectionTarget;

        if (!$class) {
            throw new Exception(
                'Unable to find reflection target. '
                . 'Have you set $defaultReflectionTarget or passed in a class name?'
            );
        }

        if (!is_object($class)) {
            return $class;
        }

        return get_class($class);
    }

    /**
     * Gets a new ReflectionMethod instance. Extracted for testing purposes.
     *
     * @param  string $class the class name
     * @param  string $method the method name
     * @return \ReflectionMethod
     */
    protected function _getNewReflectionMethod(string $class, string $method): ReflectionMethod
    {
        return new ReflectionMethod($class, $method);
    }

    /**
     * Gets a new ReflectionProperty instance. Extracted for testing purposes.
     *
     * @param  string $class the class name
     * @param  string $property the property to access/manipulate
     * @return \ReflectionProperty
     */
    protected function _getNewReflectionProperty(string $class, string $property): ReflectionProperty
    {
        return new ReflectionProperty($class, $property);
    }
}
