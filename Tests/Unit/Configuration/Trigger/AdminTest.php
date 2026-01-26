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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Admin;
use PHPUnit\Framework\TestCase;

/**
 * AdminTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AdminTest extends TestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['BE_USER']);
    }

    public function testCheckReturnsFalseWhenNoBackendUser(): void
    {
        $trigger = new Admin();
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenBackendUserIsNotAdmin(): void
    {
        $backendUser = $this->createMock(\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::class);
        $backendUser->expects(self::once())->method('isAdmin')->willReturn(false);
        $GLOBALS['BE_USER'] = $backendUser;

        $trigger = new Admin();
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueWhenBackendUserIsAdmin(): void
    {
        $backendUser = $this->createMock(\TYPO3\CMS\Core\Authentication\BackendUserAuthentication::class);
        $backendUser->expects(self::once())->method('isAdmin')->willReturn(true);
        $GLOBALS['BE_USER'] = $backendUser;

        $trigger = new Admin();
        $result = $trigger->check();
        self::assertTrue($result);
    }
}
