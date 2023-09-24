# Test utilities

This package contains support traits to ease unit testing.

## Installing via composer

You should install this package into your project using composer. To do so you
can run the following command:

```bash
composer require friendsofcake/cakephp-test-utilities
```

## Traits

At this point there are two traits:

1. [`AccessibilityHelperTrait`](#accessibilityhelpertrait) : Gain access protected properties and methods.
2. [`CompareTrait`](#comparetrait) : Assert methods, comparing to files for: HTML, JSON, XML

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

    public function setUp(): void
    {
        parent::setUp();

        $this->_compareBasePath = 'comparisons/MyTest/';
    }
}
```

#### Usage

Each of the methods acts similar to the core `assertSameAsFile` method:

```php
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

See [Cake's docs](https://book.cakephp.org/5/en/development/testing.html#comparing-test-results-to-a-file)
for more details on usage of `assertSameAsFile` on which these methods are
based.
