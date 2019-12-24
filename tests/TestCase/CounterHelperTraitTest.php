<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities\Test\TestCase;

use Cake\TestSuite\TestCase;
use FriendsOfCake\TestUtilities\CounterHelperTrait;
use PHPUnit\Framework\MockObject\Rule\InvokedAtIndex;

/**
 * @covers \FriendsOfCake\TestUtilities\CounterHelperTrait
 */
class CounterHelperTraitTest extends TestCase
{
    use \FriendsOfCake\TestUtilities\CounterHelperTrait;

    public const TRAIT_NAME = CounterHelperTrait::class;

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
                return new InvokedAtIndex($input);
            }));

        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 1', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 2', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 3', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 4', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 5', $this->Test->nextCounter()->toString());
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
                return new InvokedAtIndex($input);
            }));

        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter('Foo')->toString());
        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter('Bar')->toString());
        $this->assertSame('invoked at sequence index 1', $this->Test->nextCounter('Foo')->toString());
        $this->assertSame('invoked at sequence index 1', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 2', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 3', $this->Test->nextCounter()->toString());
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
                return new InvokedAtIndex($input);
            }));

        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter($this->Test)->toString());
        $this->assertSame('invoked at sequence index 0', $this->Test->nextCounter($this)->toString());
        $this->assertSame('invoked at sequence index 1', $this->Test->nextCounter($this->Test)->toString());
        $this->assertSame('invoked at sequence index 1', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 2', $this->Test->nextCounter()->toString());
        $this->assertSame('invoked at sequence index 3', $this->Test->nextCounter()->toString());
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
