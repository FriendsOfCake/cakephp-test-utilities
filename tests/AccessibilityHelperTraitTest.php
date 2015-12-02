<?php

namespace FriendsOfCake\TestUtilities\Test;

/**
 * @covers FriendsOfCake\TestUtilities\AccessibilityHelperTrait
 */
class AccessibilityHelperTraitTest extends \PHPUnit_Framework_TestCase
{

    use \FriendsOfCake\TestUtilities\AccessibilityHelperTrait;
    const TRAIT_NAME = 'FriendsOfCake\\TestUtilities\\AccessibilityHelperTrait';

/**
 * Mock object for the trait.
 *
 * @var \PHPUnit_Framework_MockObject_MockObject
 */
    protected $_trait = null;

/**
 * SetUp callback. Generates a new mock object for every test method.
 */
    public function setUp()
    {
        parent::setUp();
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME);
        $this->setReflectionClassInstance($this->_trait);
        $this->defaultReflectionTarget = $this->_trait;
    }

/**
 * Tests AccessibilityHelperTrait::resetReflectionCache().
 */
    public function testResetReflectionCache()
    {
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
    public function testSetReflectionClassInstance()
    {
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
    public function testGetReflectionInstanceExisting()
    {
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
    public function testGetReflectionInstanceMissing()
    {
        $this->_trait->getReflectionInstance('MyTestInstance');
    }

/**
 * Tests AccessibilityHelperTrait::callProtectedMethod().
 *
 * Call a new protected method. One that hasn't been called before.
 */
    public function testCallProtectedMethodNewInstance()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionTargetClass', '_getNewReflectionMethod', 'getReflectionInstance'
        ]);
        $this->setReflectionClassInstance($this->_trait);
        $this->defaultReflectionTarget = $this->_trait;

        $method = $this->getMock('\ReflectionMethod', [], [], '', false);

        $this->_trait->expects($this->once())
            ->method('_getReflectionTargetClass')
            ->with('FOC')
            ->will($this->returnValue('FocClass'));

        $this->_trait->expects($this->once())
            ->method('_getNewReflectionMethod')
            ->with('FocClass', 'getFOC')
            ->will($this->returnValue($method));

        $method->expects($this->once())
            ->method('setAccessible')
            ->with(true);

        $this->_trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('FocClass')
            ->will($this->returnValue($this->_trait));

        $method->expects($this->once())
            ->method('invokeArgs')
            ->with($this->_trait, [42, true])
            ->will($this->returnValue('FriendsOfCake'));

        $expected = 'FriendsOfCake';
        $actual = $this->_trait->callProtectedMethod('getFOC', [42, true], 'FOC');
        $this->assertEquals($expected, $actual);

        $expected = ['FocClass_getFOC' => $method];
        $actual = $this->getProtectedProperty('_reflectionMethodCache');
        $this->assertSame($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::callProtectedMethod().
 *
 * Call an existing protected method. One that has been called before.
 */
    public function testCallProtectedMethodExistingInstance()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionTargetClass', '_getNewReflectionMethod', 'getReflectionInstance'
        ]);
        $this->setReflectionClassInstance($this->_trait);
        $this->defaultReflectionTarget = $this->_trait;

        $method = $this->getMock('\ReflectionMethod', [], [], '', false);
        $cache = ['FocClass_getFOC' => $method];
        $this->setProtectedProperty('_reflectionMethodCache', $cache);

        $this->_trait->expects($this->once())
            ->method('_getReflectionTargetClass')
            ->with('FOC')
            ->will($this->returnValue('FocClass'));

        $this->_trait->expects($this->never())
            ->method('_getNewReflectionMethod');

        $method->expects($this->never())
            ->method('setAccessible');

        $this->_trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('FocClass')
            ->will($this->returnValue($this->_trait));

        $method->expects($this->once())
            ->method('invokeArgs')
            ->with($this->_trait, [42, true])
            ->will($this->returnValue('FriendsOfCake'));

        $expected = 'FriendsOfCake';
        $actual = $this->_trait->callProtectedMethod('getFOC', [42, true], 'FOC');
        $this->assertEquals($expected, $actual);

        $expected = $cache;
        $actual = $this->getProtectedProperty('_reflectionMethodCache');
        $this->assertSame($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::getProtectedProperty().
 */
    public function testGetProtectedProperty()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionPropertyInstance', 'getReflectionInstance'
        ]);

        $property = $this->getMock('\ReflectionProperty', [], [], '', false);

        $this->_trait->expects($this->once())
            ->method('_getReflectionPropertyInstance')
            ->with('_myProperty', 'MyClass')
            ->will($this->returnValue($property));

        $this->_trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('MyClass')
            ->will($this->returnValue($this->_trait));

        $property->expects($this->once())
            ->method('getValue')
            ->with($this->_trait)
            ->will($this->returnValue('FriendsOfCake'));

        $expected = 'FriendsOfCake';
        $actual = $this->_trait->getProtectedProperty('_myProperty', 'MyClass');
        $this->assertEquals($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::setProtectedProperty().
 */
    public function testSetProtectedProperty()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionPropertyInstance', 'getReflectionInstance'
        ]);

        $property = $this->getMock('\ReflectionProperty', [], [], '', false);

        $this->_trait->expects($this->once())
            ->method('_getReflectionPropertyInstance')
            ->with('_myProperty', 'MyClass')
            ->will($this->returnValue($property));

        $this->_trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('MyClass')
            ->will($this->returnValue($this->_trait));

        $property->expects($this->once())
            ->method('setValue')
            ->with($this->_trait, 'FriendsOfCake')
            ->will($this->returnValue('FriendsOfCake'));

        $expected = 'FriendsOfCake';
        $actual = $this->_trait->setProtectedProperty('_myProperty', 'FriendsOfCake', 'MyClass');
        $this->assertEquals($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::_getReflectionPropertyInstance().
 *
 * Without any previously stored properties.
 */
    public function testProtectedGetReflectionPropertyInstanceWithoutCache()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionTargetClass', '_getNewReflectionProperty'
        ]);
        $this->setReflectionClassInstance($this->_trait);
        $this->defaultReflectionTarget = $this->_trait;

        $property = $this->getMock('\ReflectionProperty', [], [], '', false);

        $this->_trait->expects($this->once())
            ->method('_getReflectionTargetClass')
            ->with('FocClass')
            ->will($this->returnValue('FocTargetClass'));

        $this->_trait->expects($this->once())
            ->method('_getNewReflectionProperty')
            ->with('FocTargetClass', '_focValue')
            ->will($this->returnValue($property));

        $property->expects($this->once())
            ->method('setAccessible')
            ->with(true);

        $expected = $property;
        $actual = $this->callProtectedMethod('_getReflectionPropertyInstance', ['_focValue', 'FocClass']);
        $this->assertEquals($expected, $actual);

        $expected = ['FocTargetClass__focValue' => $property];
        $actual = $this->getProtectedProperty('_reflectionPropertyCache');
        $this->assertSame($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::_getReflectionPropertyInstance().
 *
 * With a previously stored property.
 */
    public function testProtectedGetReflectionPropertyInstanceWithCache()
    {
        $this->_trait = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, [
            '_getReflectionTargetClass', '_getNewReflectionProperty'
        ]);
        $this->setReflectionClassInstance($this->_trait);
        $this->defaultReflectionTarget = $this->_trait;

        $property = $this->getMock('\ReflectionProperty', [], [], '', false);
        $cache = ['FocTargetClass__focValue' => $property];

        $this->setProtectedProperty('_reflectionPropertyCache', $cache);

        $this->_trait->expects($this->once())
            ->method('_getReflectionTargetClass')
            ->with('FocClass')
            ->will($this->returnValue('FocTargetClass'));

        $this->_trait->expects($this->never())
            ->method('_getNewReflectionProperty');

        $expected = $property;
        $actual = $this->callProtectedMethod('_getReflectionPropertyInstance', ['_focValue', 'FocClass']);
        $this->assertEquals($expected, $actual);

        $expected = $cache;
        $actual = $this->getProtectedProperty('_reflectionPropertyCache');
        $this->assertSame($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::_getReflectionTargetClass().
 *
 * With valid values.
 */
    public function testProtectedGetReflectionTargetClassValidValues()
    {
        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('_getReflectionTargetClass', ['MyClass']);
        $this->assertSame($expected, $actual);

        $expected = get_class($this);
        $actual = $this->callProtectedMethod('_getReflectionTargetClass', [$this]);
        $this->assertSame($expected, $actual);

        $this->_trait->defaultReflectionTarget = 'MyClass';

        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('_getReflectionTargetClass', [null]);
        $this->assertSame($expected, $actual);

        $this->_trait->defaultReflectionTarget = $this;

        $expected = get_class($this);
        $actual = $this->callProtectedMethod('_getReflectionTargetClass', [null]);
        $this->assertSame($expected, $actual);

        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('_getReflectionTargetClass', ['MyClass']);
        $this->assertSame($expected, $actual);
    }

/**
 * Tests AccessibilityHelperTrait::_getReflectionTargetClass().
 *
 * With invalid values to trigger the exception.
 *
 * @expectedException \Exception
 */
    public function testProtectedGetReflectionTargetClassInvalidValues()
    {
        $this->callProtectedMethod('_getReflectionTargetClass', [null]);
    }
}
