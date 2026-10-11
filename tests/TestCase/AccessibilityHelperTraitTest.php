<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use Exception;
use FriendsOfCake\TestUtilities\AccessibilityHelperTrait;
use FriendsOfCake\TestUtilities\Test\TestSuite\Fixture\AccessibilityHelperTraitMock;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \FriendsOfCake\TestUtilities\AccessibilityHelperTrait
 */
class AccessibilityHelperTraitTest extends TestCase
{
    use AccessibilityHelperTrait;

    public const TRAIT_NAME = AccessibilityHelperTrait::class;

    /**
     * Test double for the trait.
     *
     * @var \FriendsOfCake\TestUtilities\Test\TestSuite\Fixture\AccessibilityHelperTraitMock|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $trait = null;

    /**
     * SetUp callback. Generates a new test double for every test method.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->trait = new AccessibilityHelperTraitMock();
        $this->setReflectionClassInstance($this->trait);
        $this->defaultReflectionTarget = $this->trait;
    }

    /**
     * Creates a mock of the trait double with only the given methods mocked.
     *
     * @param list<string> $methods
     * @return \FriendsOfCake\TestUtilities\Test\TestSuite\Fixture\AccessibilityHelperTraitMock&\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createTraitMock(array $methods): AccessibilityHelperTraitMock&MockObject
    {
        return $this->getMockBuilder(AccessibilityHelperTraitMock::class)
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * Tests AccessibilityHelperTrait::resetReflectionCache().
     */
    public function testResetReflectionCache(): void
    {
        $this->setProtectedProperty('reflectionPropertyCache', ['reflectionPropertyCache']);
        $this->setProtectedProperty('reflectionMethodCache', ['reflectionMethodCache']);
        $this->setProtectedProperty('reflectionInstanceCache', ['reflectionInstanceCache']);

        $this->assertNotEmpty($this->getProtectedProperty('reflectionPropertyCache'));
        $this->assertNotEmpty($this->getProtectedProperty('reflectionMethodCache'));
        $this->assertNotEmpty($this->getProtectedProperty('reflectionInstanceCache'));

        $this->trait->resetReflectionCache();

        $this->assertEmpty($this->getProtectedProperty('reflectionPropertyCache'));
        $this->assertEmpty($this->getProtectedProperty('reflectionMethodCache'));
        $this->assertEmpty($this->getProtectedProperty('reflectionInstanceCache'));
    }

    /**
     * Tests AccessibilityHelperTrait::setReflectionClassInstance().
     */
    public function testSetReflectionClassInstance(): void
    {
        $this->trait->setReflectionClassInstance($this);
        $this->trait->setReflectionClassInstance($this, 'MyTestInstance');
        $this->trait->setReflectionClassInstance($this->trait, 'MyTestInstance');

        $expected = [
            static::class => $this,
            'MyTestInstance' => $this->trait,
        ];
        $actual = $this->getProtectedProperty('reflectionInstanceCache');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::setReflectionClassInstance().
     *
     * Get existing objects from the cache.
     */
    public function testGetReflectionInstanceExisting(): void
    {
        $this->trait->setReflectionClassInstance($this, 'MyTestInstance');

        $expected = $this;
        $actual = $this->trait->getReflectionInstance('MyTestInstance');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::setReflectionClassInstance().
     *
     * Get missing objects from the cache.
     */
    public function testGetReflectionInstanceMissing(): void
    {
        $this->expectException(Exception::class);

        $this->trait->getReflectionInstance('MyTestInstance');
    }

    /**
     * Tests AccessibilityHelperTrait::callProtectedMethod().
     *
     * Call a new protected method. One that hasn't been called before.
     */
    public function testCallProtectedMethodNewInstance(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionTargetClass', 'getNewReflectionMethod', 'getReflectionInstance',
        ]);
        $this->setReflectionClassInstance($this->trait);
        $this->defaultReflectionTarget = $this->trait;

        $method = $this->getMockBuilder('\ReflectionMethod')
            ->disableOriginalConstructor()
            ->getMock();

        $this->trait->expects($this->once())
            ->method('getReflectionTargetClass')
            ->with('FOC')
            ->willReturn('FocClass');

        $this->trait->expects($this->once())
            ->method('getNewReflectionMethod')
            ->with('FocClass', 'getFOC')
            ->willReturn($method);

        $this->trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('FocClass')
            ->willReturn($this->trait);

        $method->expects($this->once())
            ->method('invokeArgs')
            ->with($this->trait, [42, true])
            ->willReturn('FriendsOfCake');

        $expected = 'FriendsOfCake';
        $actual = $this->trait->callProtectedMethod('getFOC', [42, true], 'FOC');
        $this->assertEquals($expected, $actual);

        $expected = ['FocClass_getFOC' => $method];
        $actual = $this->getProtectedProperty('reflectionMethodCache');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::callProtectedMethod().
     *
     * Call an existing protected method. One that has been called before.
     */
    public function testCallProtectedMethodExistingInstance(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionTargetClass', 'getNewReflectionMethod', 'getReflectionInstance',
        ]);
        $this->setReflectionClassInstance($this->trait);
        $this->defaultReflectionTarget = $this->trait;

        $method = $this->getMockBuilder('\ReflectionMethod')
            ->disableOriginalConstructor()
            ->getMock();
        $cache = ['FocClass_getFOC' => $method];
        $this->setProtectedProperty('reflectionMethodCache', $cache);

        $this->trait->expects($this->once())
            ->method('getReflectionTargetClass')
            ->with('FOC')
            ->willReturn('FocClass');

        $this->trait->expects($this->never())
            ->method('getNewReflectionMethod');

        $this->trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('FocClass')
            ->willReturn($this->trait);

        $method->expects($this->once())
            ->method('invokeArgs')
            ->with($this->trait, [42, true])
            ->willReturn('FriendsOfCake');

        $expected = 'FriendsOfCake';
        $actual = $this->trait->callProtectedMethod('getFOC', [42, true], 'FOC');
        $this->assertEquals($expected, $actual);

        $expected = $cache;
        $actual = $this->getProtectedProperty('reflectionMethodCache');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::getProtectedProperty().
     */
    public function testGetProtectedProperty(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionPropertyInstance', 'getReflectionInstance',
        ]);

        $property = $this->getMockBuilder('\ReflectionProperty')
            ->disableOriginalConstructor()
            ->getMock();

        $this->trait->expects($this->once())
            ->method('getReflectionPropertyInstance')
            ->with('_myProperty', 'MyClass')
            ->willReturn($property);

        $this->trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('MyClass')
            ->willReturn($this->trait);

        $property->expects($this->once())
            ->method('getValue')
            ->with($this->trait)
            ->willReturn('FriendsOfCake');

        $expected = 'FriendsOfCake';
        $actual = $this->trait->getProtectedProperty('_myProperty', 'MyClass');
        $this->assertEquals($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::setProtectedProperty().
     */
    public function testSetProtectedProperty(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionPropertyInstance', 'getReflectionInstance',
        ]);

        $property = $this->getMockBuilder('\ReflectionProperty')
            ->disableOriginalConstructor()
            ->getMock();

        $this->trait->expects($this->once())
            ->method('getReflectionPropertyInstance')
            ->with('_myProperty', 'MyClass')
            ->willReturn($property);

        $this->trait->expects($this->once())
            ->method('getReflectionInstance')
            ->with('MyClass')
            ->willReturn($this->trait);

        $property->expects($this->once())
            ->method('setValue')
            ->with($this->trait, 'FriendsOfCake');

        $this->trait->setProtectedProperty('_myProperty', 'FriendsOfCake', 'MyClass');
    }

    /**
     * Tests AccessibilityHelperTrait::getReflectionPropertyInstance().
     *
     * Without any previously stored properties.
     */
    public function testProtectedGetReflectionPropertyInstanceWithoutCache(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionTargetClass', 'getNewReflectionProperty',
        ]);
        $this->setReflectionClassInstance($this->trait);
        $this->defaultReflectionTarget = $this->trait;

        $property = $this->createStub('\ReflectionProperty');

        $this->trait->expects($this->once())
            ->method('getReflectionTargetClass')
            ->with('FocClass')
            ->willReturn('FocTargetClass');

        $this->trait->expects($this->once())
            ->method('getNewReflectionProperty')
            ->with('FocTargetClass', '_focValue')
            ->willReturn($property);

        $expected = $property;
        $actual = $this->callProtectedMethod('getReflectionPropertyInstance', ['_focValue', 'FocClass']);
        $this->assertEquals($expected, $actual);

        $expected = ['FocTargetClass__focValue' => $property];
        $actual = $this->getProtectedProperty('reflectionPropertyCache');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::getReflectionPropertyInstance().
     *
     * With a previously stored property.
     */
    public function testProtectedGetReflectionPropertyInstanceWithCache(): void
    {
        $this->trait = $this->createTraitMock([
            'getReflectionTargetClass', 'getNewReflectionProperty',
        ]);
        $this->setReflectionClassInstance($this->trait);
        $this->defaultReflectionTarget = $this->trait;

        $property = $this->createStub('\ReflectionProperty');
        $cache = ['FocTargetClass__focValue' => $property];

        $this->setProtectedProperty('reflectionPropertyCache', $cache);

        $this->trait->expects($this->once())
            ->method('getReflectionTargetClass')
            ->with('FocClass')
            ->willReturn('FocTargetClass');

        $this->trait->expects($this->never())
            ->method('getNewReflectionProperty');

        $expected = $property;
        $actual = $this->callProtectedMethod('getReflectionPropertyInstance', ['_focValue', 'FocClass']);
        $this->assertEquals($expected, $actual);

        $expected = $cache;
        $actual = $this->getProtectedProperty('reflectionPropertyCache');
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::getReflectionTargetClass().
     *
     * With valid values.
     */
    public function testProtectedGetReflectionTargetClassValidValues(): void
    {
        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('getReflectionTargetClass', ['MyClass']);
        $this->assertSame($expected, $actual);

        $expected = static::class;
        $actual = $this->callProtectedMethod('getReflectionTargetClass', [$this]);
        $this->assertSame($expected, $actual);

        $this->trait->defaultReflectionTarget = 'MyClass';

        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('getReflectionTargetClass', [null]);
        $this->assertSame($expected, $actual);

        $this->trait->defaultReflectionTarget = $this;

        $expected = static::class;
        $actual = $this->callProtectedMethod('getReflectionTargetClass', [null]);
        $this->assertSame($expected, $actual);

        $expected = 'MyClass';
        $actual = $this->callProtectedMethod('getReflectionTargetClass', ['MyClass']);
        $this->assertSame($expected, $actual);
    }

    /**
     * Tests AccessibilityHelperTrait::getReflectionTargetClass().
     *
     * With invalid values to trigger the exception.
     */
    public function testProtectedGetReflectionTargetClassInvalidValues(): void
    {
        $this->expectException(Exception::class);

        $this->callProtectedMethod('getReflectionTargetClass', [null]);
    }
}
