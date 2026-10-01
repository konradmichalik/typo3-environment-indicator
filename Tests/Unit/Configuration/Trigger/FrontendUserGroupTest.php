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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\FrontendUserGroup;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * FrontendUserGroupTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrontendUserGroupTest extends TestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    public function testCheckReturnsFalseWhenNoRequest(): void
    {
        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenNoFrontendUser(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn(null);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenNoUserGroups(): void
    {
        $this->setRequestWithFrontendUserGroupData([]);

        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueWhenUserIsInOneOfMultipleGroups(): void
    {
        $this->setRequestWithFrontendUserGroupData(['uid' => [1, 2, 3]]);

        $trigger = new FrontendUserGroup(4, 5, 2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseWhenUserIsNotInAnyGroup(): void
    {
        $this->setRequestWithFrontendUserGroupData(['uid' => [1, 2, 3]]);

        $trigger = new FrontendUserGroup(4, 5, 6);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    /**
     * @param array<string, mixed> $groupData
     */
    private function setRequestWithFrontendUserGroupData(array $groupData): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = $groupData;

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;
    }
}
