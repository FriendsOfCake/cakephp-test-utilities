# Test utilities

This package contains support traits to ease unit testing.

## Installing via composer

You should install this package into your project using composer. To do so you
can run the following command:

```composer require friendsofcake/cakephp-test-utilities```

## Traits

The usage of these traits requires at least PHP 5.4. At this point there are
two traits:

1. [`AccessibilityHelperTrait`](#accessibilityhelpertrait) : Gain access protected properties and methods.
2. [`CounterHelperTrait`](#counterhelpertrait) : Uses counters to help with the order of expectations.

### AccessibilityHelperTrait

This trait gains you access to protected properties and methods. You don't need
of a new class with pass-through methods. It uses reflection to achieve this.

#### Setup

Add the trait at the top of your test case:

``` php
use \FriendsOfCake\TestUtilities\AccessibilityHelperTrait;
```

Now that you have the trait you need to set which object you want to access.
You can do this globally for the entire test in `setUp()` or in your test
methods:

``` php
$object = new ObjectIAmGoingToTest();
$this->setReflectionClassInstance($object);
$this->defaultReflectionTarget = $object; // (optional)
```

#### Protected properties

You can get and set the protected properties:

``` php
$data = 'FriendsOfCake';
$this->setProtectedProperty('_myProperty', $data, $object);

$expected = $data;
$actual = $this->getProtectedProperty('_myProperty', $object);
$this->assertEquals($expected, $actual);
```

#### Protected methods

You can directly call protected methods:

``` php
$parameters = [$argument1, $argument2];

$expected = $expectedReturnValue;
$actual = $this->callProtectedMethod('_myMethod', $parameters, $object);
$this->assertEquals($expected, $actual);
```

### CompareTrait

This trait helps with comparing test results as string

#### Setup

Add the trait at the top of your test case and define the `_compareBasePath`
property so the trait knows where to look for comparison files:

``` php
...
use \FriendsOfCake\TestUtilities\CompareTrait;

class MyTest extends TestCase
{
    use CompareTrait;

    public function setUp()
    {
        parent::setUp();
        $this->_compareBasePath = 'comparisons/MyTest/';
    }
}

#### Usage

Each of the methods acts similar to the core `assertSameAsFile` method:

```
public function testExample()
{
    $html = '<p>Some html</p>';
    $xml = '<?xml version="1.0" encoding="UTF-8"?><thing>...</thing>';
    $json = ['actually' => 'this is an array'];

    $this->assertHtmlSameAsFile('some.html', $html);
    $this->assertXmlSameAsFile('some.xml', $xml);
    $this->assertJsonSameAsFile('some.json', $json);
}
```

See [Cake's docs](https://book.cakephp.org/3.0/en/development/testing.html#comparing-test-results-to-a-file)
for more details on usage of `assertSameAsFile` on which these methods are
based.

### CounterHelperTrait

This trait helps with defining expectations that are order specific.

#### Setup

Add the trait at the top of your test case:

``` php
use \FriendsOfCake\TestUtilities\CounterHelperTrait;
```

That's it.

#### Single mock objects

Usually you would do something similar to this to set orders for your mock
objects:

``` php
$mock->expects($this->at(0))
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('myFirstReturnValue'));

$mock->expects($this->at(1))
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('mySecondReturnValue'));
```

Instead this trait implements a `CounterHelperTrait::next()` method. It will
track the indices for you, so you can easily switch calls or add some later,
without having to change them. Example:

``` php
$mock->expects($this->nextCounter()) // = $this->at(0)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('myFirstReturnValue'));

$mock->expects($this->nextCounter()) // = $this->at(1)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('mySecondReturnValue'));
```

#### Multiple mock objects

If you have multiple mock objects you need to use multiple independent
counters. For this to work you need to identify which counter you want to use
by passing an object (or a string):

``` php
$mock1->expects($this->nextCounter($mock1)) // = $this->at(0)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('myFirstReturnValue'));

$mock2->expects($this->nextCounter($mock2)) // = $this->at(0)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('myFirstReturnValue'));

$mock1->expects($this->nextCounter($mock1)) // = $this->at(1)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('mySecondReturnValue'));

$mock2->expects($this->nextCounter($mock2)) // = $this->at(1)
    ->method('myMethod')
    ->with('myParameter')
    ->will($this->returnValue('mySecondReturnValue'));
```
