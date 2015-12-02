<?php

namespace FriendsOfCake\TestUtilities;

/**
 *
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
    protected $_expectationCounters = [];

/**
 * Returns a matcher that matches when the method it is evaluated for
 * is invoked _next_
 *
 * Permits using multiple named counters
 *
 * @param mixed $name string or object
 * @return \PHPUnit_Framework_MockObject_Matcher_InvokedAtIndex
 */
    public function next($name = '')
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
