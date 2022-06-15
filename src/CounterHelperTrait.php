<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities;

use PHPUnit\Framework\MockObject\Rule\InvokedAtIndex;

/**
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Andy Dawson, 2013
 */
trait CounterHelperTrait
{
    /**
     * List of counters used by this test case
     *
     * @var array
     */
    protected array $_expectationCounters = [];

    /**
     * Returns a matcher that matches when the method it is evaluated for
     * is invoked _next_
     *
     * Permits using multiple named counters
     *
     * @param string|object $name string or object
     * @return \PHPUnit\Framework\MockObject\Rule\InvokedAtIndex
     */
    public function nextCounter(string|object $name = ''): InvokedAtIndex
    {
        if (is_object($name)) {
            $name = get_class($name);
        }
        if (!isset($this->_expectationCounters[$name])) {
            $this->_expectationCounters[$name] = 0;
        } else {
            $this->_expectationCounters[$name] += 1;
        }

        return $this->at($this->_expectationCounters[$name]);
    }
}
