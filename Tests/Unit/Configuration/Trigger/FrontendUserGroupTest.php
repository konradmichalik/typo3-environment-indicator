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

    public function testConstructorAcceptsSingleGroup(): void
    {
        $trigger = new FrontendUserGroup(1);
        self::assertInstanceOf(FrontendUserGroup::class, $trigger);
    }

    public function testConstructorAcceptsMultipleGroups(): void
    {
        $trigger = new FrontendUserGroup(1, 2, 3);
        self::assertInstanceOf(FrontendUserGroup::class, $trigger);
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
        $request->method('getAttribute')->with('frontend.user')->willReturn(null);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenNoUserGroups(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = [];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueWhenUserIsInMatchingGroup(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = ['uid' => [1, 2, 3]];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsTrueWhenUserIsInOneOfMultipleGroups(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = ['uid' => [1, 2, 3]];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(4, 5, 2);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseWhenUserIsNotInAnyGroup(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = ['uid' => [1, 2, 3]];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(4, 5, 6);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsFalseWhenUserHasEmptyGroups(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = ['uid' => []];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $trigger = new FrontendUserGroup(1);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckUsesStrictComparison(): void
    {
        $frontendUser = $this->createStub(FrontendUserAuthentication::class);
        $frontendUser->groupData = ['uid' => [1, 2, 3]];

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getAttribute')->with('frontend.user')->willReturn($frontendUser);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        // Test that int 4 doesn't match any group to ensure strict comparison
        $trigger = new FrontendUserGroup(4);
        $result = $trigger->check();
        self::assertFalse($result);
    }
}
