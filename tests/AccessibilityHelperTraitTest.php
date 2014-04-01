<?php

namespace FriendsOfCake\TestUtilities\Test;

/**
 * @covers FriendsOfCake\TestUtilities\AccessibilityHelperTrait
 */
class AccessibilityHelperTraitTest extends \PHPUnit_Framework_TestCase {

	use \FriendsOfCake\TestUtilities\AccessibilityHelperTrait;

/**
 * Mock object for the trait.
 *
 * @var \PHPUnit_Framework_MockObject_MockObject
 */
	protected $_trait = null;

/**
 * SetUp callback. Generates a new mock object for every test method.
 */
	public function setUp() {
		parent::setUp();
		$trait = 'FriendsOfCake\\TestUtilities\\AccessibilityHelperTrait';
		$this->_trait = $this->getMockForTrait($trait);
		$this->setReflectionClassInstance($this->_trait);
		$this->defaultReflectionTarget = $this->_trait;
	}

/**
 * Tests AccessibilityHelperTrait::resetReflectionCache().
 */
	public function testResetReflectionCache() {
		$this->setProtectedProperty('_reflectionPropertyCache', ['_reflectionPropertyCache']);
		$this->setProtectedProperty('_reflectionMethodCache', ['_reflectionMethodCache']);
		$this->setProtectedProperty('_reflectionInstanceCache', ['_reflectionInstanceCache']);

		$this->assertNotEmpty($this->getProtectedProperty('_reflectionPropertyCache'));
		$this->assertNotEmpty($this->getProtectedProperty('_reflectionMethodCache'));
		$this->assertNotEmpty($this->getProtectedProperty('_reflectionInstanceCache'));

		$this->_trait->resetReflectionCache();

		$this->assertEmpty($this->getProtectedProperty('_reflectionPropertyCache'));
		$this->assertEmpty($this->getProtectedProperty('_reflectionMethodCache'));
		$this->assertEmpty($this->getProtectedProperty('_reflectionInstanceCache'));
	}

/**
 * Tests AccessibilityHelperTrait::setReflectionClassInstance().
 */
	public function testSetReflectionClassInstance() {
		$this->_trait->setReflectionClassInstance($this);
		$this->_trait->setReflectionClassInstance($this, 'MyTestInstance');
		$this->_trait->setReflectionClassInstance($this->_trait, 'MyTestInstance');

		$expected = [
			get_class($this) => $this,
			'MyTestInstance' => $this->_trait
		];
		$actual = $this->getProtectedProperty('_reflectionInstanceCache');
		$this->assertSame($expected, $actual);
	}

/**
 * Tests AccessibilityHelperTrait::setReflectionClassInstance().
 *
 * Get existing objects from the cache.
 */
	public function testGetReflectionInstanceExisting() {
		$this->_trait->setReflectionClassInstance($this, 'MyTestInstance');

		$expected = $this;
		$actual = $this->_trait->getReflectionInstance('MyTestInstance');
		$this->assertSame($expected, $actual);
	}

/**
 * Tests AccessibilityHelperTrait::setReflectionClassInstance().
 *
 * Get missing objects from the cache.
 *
 * @expectedException Exception
 */
	public function testGetReflectionInstanceMissing() {
		$this->_trait->getReflectionInstance('MyTestInstance');
	}

}
