<?php

namespace FriendsOfCake\TestUtilities;

/**
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Christian Winther, 2013
 */
trait AccessibilityHelperTrait {

/**
 * List of Reflection properties made public.
 *
 * @var array
 */
	protected $_reflectionPropertyCache = array();

/**
 * List of Reflection methods made public.
 *
 * @var array
 */
	protected $_reflectionMethodCache = array();

/**
 * List of class names <=> instances used for invocation.
 *
 * @var array
 */
	protected $_reflectionInstanceCache = array();

/**
 * Reset the internal reflection caches.
 *
 * @return void
 */
	public function resetReflectionCache() {
		$this->_reflectionPropertyCache = array();
		$this->_reflectionMethodCache = array();
		$this->_reflectionInstanceCache = array();
	}

/**
 * Map an instance of an object to its class name.
 *
 * @param Object $instance
 * @return void
 */
	public function setReflectionClassInstance($instance, $class = null) {
		$class = $class ?: get_class($instance);
		$this->_reflectionInstanceCache[$class] = $instance;
	}

/**
 * Get working instance of "$class".
 *
 * @param string $class
 * @return Object
 */
	public function getReflectionInstance($class) {
		$class = $this->_getReflectionTargetClass($class);
		if (empty($this->_reflectionInstanceCache[$class])) {
			throw new \Exception(sprintf('Unable to find instance of %s in the reflection cache. Have you added it using "setReflectionClassInstance"?', $class));
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
	public function callProtectedMethod($method, $args = array(), $class = null) {
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
	public function getProtectedProperty($property, $class = null) {
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
	public function setProtectedProperty($property, $value, $class = null) {
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
	protected function _getReflectionPropertyInstance($property, $class) {
		$class = $this->_getReflectionTargetClass($class);
		$cacheKey = $class . '_' . $property;

		if (!in_array($cacheKey, $this->_reflectionPropertyCache)) {
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
 */
	protected function _getReflectionTargetClass($class) {
		if (is_object($class)) {
			$class = get_class($class);
		}

		if (!empty($class)) {
			return $class;
		}

		if (isset($this->defaultReflectionTarget)) {
			$class = $this->defaultReflectionTarget;
			if (is_object($class)) {
				$class = get_class($class);
			}
		}

		if (empty($class)) {
			throw new \Exception(sprintf('Unable to find reflection target; have you set $defaultReflectionTarget or passed in class name?', $class));
		}

		return $class;
	}

/**
 * Gets a new ReflectionMethod instance. Extracted for testing purposes.
 *
 * @param mixed $class
 * @param string $method
 * @return \ReflectionMethod
 */
	protected function _getNewReflectionMethod($class, $method) {
		return new \ReflectionMethod($class, $method);
	}

/**
 * Gets a new ReflectionProperty instance. Extracted for testing purposes.
 *
 * @param mixed $class
 * @param string $property
 * @return \ReflectionProperty
 */
	protected function _getNewReflectionProperty($class, $property) {
		return new \ReflectionProperty($class, $property);
	}
}
