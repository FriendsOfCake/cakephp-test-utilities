# Test utilities

This package contains support traits to ease unit testing.

## Installing via composer

You should install this package into your project using composer. To do so you can add the following to your composer.json file:

``` json
"require-dev": {
    "jippi/cakephp-test-utilities": "dev-master"
}

```

## Traits

The usage of these traits requires at least PHP 5.4. At this point there is one trait:

1. `AccessibilityHelperTrait` : Gain access protected properties and methods.

### AccessibilityHelperTrait

This trait gains you access to protected properties and methods. You don't need of a new class with pass-through methods. It uses reflection to achieve this.

#### Setup

Add the trait at the top of your test case:

``` php
use \FriendsOfCake\TestUtilities\AccessibilityHelperTrait;
```

Now that you have the trait you need to set which object you want to access. You can do this globally for the entire test in `setUp()` or in your test methods:

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
