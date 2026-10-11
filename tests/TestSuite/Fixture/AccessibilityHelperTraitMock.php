<?php
declare(strict_types=1);

namespace FriendsOfCake\TestUtilities\Test\TestSuite\Fixture;

use FriendsOfCake\TestUtilities\AccessibilityHelperTrait;

/**
 * Concrete test double for AccessibilityHelperTrait.
 *
 * PHPUnit no longer supports mocking traits directly, so tests mock
 * this class (optionally with onlyMethods()) instead.
 */
class AccessibilityHelperTraitMock
{
    use AccessibilityHelperTrait;
}
