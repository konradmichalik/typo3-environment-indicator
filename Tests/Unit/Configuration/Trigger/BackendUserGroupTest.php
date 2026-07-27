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

use KonradMichalik\Ttt\Attribute\WithBackendUser;
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

    #[WithBackendUser(groups: [])]
    public function testCheckReturnsFalseWhenNoUserGroups(): void
    {
        $trigger = new BackendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    #[WithBackendUser(groups: [1, 2, 3])]
    public function testCheckReturnsTrueWhenUserIsInMatchingGroup(): void
    {
        $trigger = new BackendUserGroup(2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    #[WithBackendUser(groups: [1, 2, 3])]
    public function testCheckReturnsTrueWhenUserIsInOneOfMultipleGroups(): void
    {
        $trigger = new BackendUserGroup(4, 5, 2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    #[WithBackendUser(groups: [1, 2, 3])]
    public function testCheckReturnsFalseWhenUserIsNotInAnyGroup(): void
    {
        $trigger = new BackendUserGroup(4, 5, 6);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    #[WithBackendUser(groups: [])]
    public function testCheckReturnsFalseWhenUserHasEmptyGroups(): void
    {
        $trigger = new BackendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    #[WithBackendUser(groups: [1, 2, 3])]
    public function testCheckUsesStrictComparison(): void
    {
        $trigger = new BackendUserGroup(4);
        $result = $trigger->check();
        self::assertFalse($result);
    }
}
