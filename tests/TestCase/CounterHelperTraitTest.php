<?php

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use PHPUnit\Framework\MockObject\Matcher\InvokedAtIndex;

/**
 * @covers FriendsOfCake\TestUtilities\CounterHelperTrait
 */
class CounterHelperTest extends TestCase
{

    use \FriendsOfCake\TestUtilities\CounterHelperTrait;
    const TRAIT_NAME = '\\FriendsOfCake\\TestUtilities\\CounterHelperTrait';

/**
 * testNext
 *
 * Tests whether nextCounter() returns the right instance.
 *
 * @return void
 */
    public function testNextCounter()
    {
        $next = $this->nextCounter();
        $this->assertInstanceOf(InvokedAtIndex::class, $next);
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

        $this->assertSame(0, $this->Test->nextCounter());
        $this->assertSame(1, $this->Test->nextCounter());
        $this->assertSame(2, $this->Test->nextCounter());
        $this->assertSame(3, $this->Test->nextCounter());
        $this->assertSame(4, $this->Test->nextCounter());
        $this->assertSame(5, $this->Test->nextCounter());
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

        $this->assertSame(0, $this->Test->nextCounter());
        $this->assertSame(0, $this->Test->nextCounter('Foo'));
        $this->assertSame(0, $this->Test->nextCounter('Bar'));
        $this->assertSame(1, $this->Test->nextCounter('Foo'));
        $this->assertSame(1, $this->Test->nextCounter());
        $this->assertSame(2, $this->Test->nextCounter());
        $this->assertSame(3, $this->Test->nextCounter());
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

        $this->assertSame(0, $this->Test->nextCounter());
        $this->assertSame(0, $this->Test->nextCounter($this->Test));
        $this->assertSame(0, $this->Test->nextCounter($this));
        $this->assertSame(1, $this->Test->nextCounter($this->Test));
        $this->assertSame(1, $this->Test->nextCounter());
        $this->assertSame(2, $this->Test->nextCounter());
        $this->assertSame(3, $this->Test->nextCounter());
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
        $next = $this->nextCounter();
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
        $next = $this->nextCounter();
        $this->assertSame('invoked at sequence index 0', $next->toString());
    }
}
