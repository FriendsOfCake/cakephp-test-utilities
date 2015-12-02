<?php

namespace FriendsOfCake\TestUtilities\Test;

/**
 * @covers FriendsOfCake\TestUtilities\CounterHelperTrait
 */
class CounterHelperTest extends \PHPUnit_Framework_TestCase
{

    use \FriendsOfCake\TestUtilities\CounterHelperTrait;
    const TRAIT_NAME = '\\FriendsOfCake\\TestUtilities\\CounterHelperTrait';

/**
 * testNext
 *
 * Tests whether next() returns the right instance.
 *
 * @return void
 */
    public function testNext()
    {
        $next = $this->next();
        $this->assertInstanceOf('PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex', $next);
    }

/**
 * testCounters
 *
 * Tests increments.
 *
 * @return void
 */
    public function testCounters()
    {
        $this->Test = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, ['at']);
        $this->Test
            ->expects($this->any())
            ->method('at')
            ->will($this->returnCallback(function ($input) {
                return $input;
            }));

        $this->assertSame(0, $this->Test->next());
        $this->assertSame(1, $this->Test->next());
        $this->assertSame(2, $this->Test->next());
        $this->assertSame(3, $this->Test->next());
        $this->assertSame(4, $this->Test->next());
        $this->assertSame(5, $this->Test->next());
    }

/**
 * testCountersNamed
 *
 * Tests multiple counters identified by a string.
 *
 * @return void
 */
    public function testCountersNamed()
    {
        $this->Test = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, ['at']);
        $this->Test
            ->expects($this->any())
            ->method('at')
            ->will($this->returnCallback(function ($input) {
                return $input;
            }));

        $this->assertSame(0, $this->Test->next());
        $this->assertSame(0, $this->Test->next('Foo'));
        $this->assertSame(0, $this->Test->next('Bar'));
        $this->assertSame(1, $this->Test->next('Foo'));
        $this->assertSame(1, $this->Test->next());
        $this->assertSame(2, $this->Test->next());
        $this->assertSame(3, $this->Test->next());
    }

/**
 * testCountersObjectNamed
 *
 * Tests multiple counters identified by an object.
 *
 * @return void
 */
    public function testCountersObjectNamed()
    {
        $this->Test = $this->getMockForTrait(self::TRAIT_NAME, [], '', true, true, true, ['at']);
        $this->Test
            ->expects($this->any())
            ->method('at')
            ->will($this->returnCallback(function ($input) {
                return $input;
            }));

        $this->assertSame(0, $this->Test->next());
        $this->assertSame(0, $this->Test->next($this->Test));
        $this->assertSame(0, $this->Test->next($this));
        $this->assertSame(1, $this->Test->next($this->Test));
        $this->assertSame(1, $this->Test->next());
        $this->assertSame(2, $this->Test->next());
        $this->assertSame(3, $this->Test->next());
    }

/**
 * testCountersDontPersist
 *
 * This is a left over from a merge. Not sure if it is still relevant.
 *
 * @return void
 */
    public function testCountersDontPersist()
    {
        $next = $this->next();
        $this->assertSame('invoked at sequence index 0', $next->toString());
    }

/**
 * testCountersDontPersist2
 *
 * Verify that by default the internal state of the counters, does not persist
 * between tests
 *
 * @return void
 */
    public function testCountersDontPersist2()
    {
        $next = $this->next();
        $this->assertSame('invoked at sequence index 0', $next->toString());
    }
}
