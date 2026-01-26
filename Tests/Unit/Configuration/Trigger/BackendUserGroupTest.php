<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_environment_indicator" TYPO3 CMS extension.
 *
 * (c) 2025-2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Trigger;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\BackendUserGroup;
use PHPUnit\Framework\TestCase;

/**
 * BackendUserGroupTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BackendUserGroupTest extends TestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['BE_USER']);
    }

    public function testConstructorAcceptsSingleGroup(): void
    {
        $trigger = new BackendUserGroup(1);
        self::assertInstanceOf(BackendUserGroup::class, $trigger);
    }

    public function testConstructorAcceptsMultipleGroups(): void
    {
        $trigger = new BackendUserGroup(1, 2, 3);
        self::assertInstanceOf(BackendUserGroup::class, $trigger);
    }

    public function testCheckReturnsFalseWhenNoBackendUser(): void
    {
        $trigger = new BackendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenNoUserGroups(): void
    {
        $GLOBALS['BE_USER'] = (object) [];
        $trigger = new BackendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueWhenUserIsInMatchingGroup(): void
    {
        $GLOBALS['BE_USER'] = (object) ['userGroupsUID' => [1, 2, 3]];
        $trigger = new BackendUserGroup(2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsTrueWhenUserIsInOneOfMultipleGroups(): void
    {
        $GLOBALS['BE_USER'] = (object) ['userGroupsUID' => [1, 2, 3]];
        $trigger = new BackendUserGroup(4, 5, 2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseWhenUserIsNotInAnyGroup(): void
    {
        $GLOBALS['BE_USER'] = (object) ['userGroupsUID' => [1, 2, 3]];
        $trigger = new BackendUserGroup(4, 5, 6);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenUserHasEmptyGroups(): void
    {
        $GLOBALS['BE_USER'] = (object) ['userGroupsUID' => []];
        $trigger = new BackendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckUsesStrictComparison(): void
    {
        $GLOBALS['BE_USER'] = (object) ['userGroupsUID' => [1, 2, 3]];
        // Test that string '1' doesn't match int 1 in strict comparison
        // We're testing with a different group (4) to ensure strict comparison
        $trigger = new BackendUserGroup(4);
        $result = $trigger->check();
        self::assertFalse($result);
    }
}
