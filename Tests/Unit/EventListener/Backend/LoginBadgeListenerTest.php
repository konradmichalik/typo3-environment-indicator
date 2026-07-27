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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\EventListener\Backend;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Backend\LoginBadgeListener;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LoginBadgeListenerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class LoginBadgeListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    public function testNothingIsInjectedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');
        $pageRenderer->expects(self::never())->method('addJsFooterInlineCode');

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [], 'resolved' => true]]])]
    public function testNothingIsInjectedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');
        $pageRenderer->expects(self::never())->method('addJsFooterInlineCode');

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Login::class => ['text' => 'STAGING', 'color' => '#00ACC1', 'description' => 'DB synced nightly']],
        'resolved' => true,
    ]]])]
    public function testBadgeIsInjectedWithTextAndDescription(): void
    {
        $this->mockExtensionConfiguration(true);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_login',
                self::logicalAnd(
                    self::stringContains('#00ACC1'),
                    self::stringContains('body>.typo3-environment-indicator-login{position:fixed;left:0;right:0;top:0;'),
                ),
                null,
                false,
                true,
            );
        $pageRenderer->expects(self::once())
            ->method('addJsFooterInlineCode')
            ->with(
                Configuration::EXT_KEY.'_login',
                self::callback(static fn (string $js): bool => str_contains($js, 'STAGING') && str_contains($js, 'DB synced nightly')),
                null,
                false,
                true,
            );

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Login::class => ['text' => 'STAGING', 'color' => '#00ACC1', 'position' => 'bottom']],
        'resolved' => true,
    ]]])]
    public function testBottomPositionAppendsAfterCardInsteadOfBeforeIt(): void
    {
        $this->mockExtensionConfiguration(true);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                self::anything(),
                self::logicalAnd(
                    self::stringContains('border-bottom-left-radius:inherit'),
                    self::logicalNot(self::stringContains('border-top-left-radius:inherit')),
                    self::stringContains('body>.typo3-environment-indicator-login{position:fixed;left:0;right:0;bottom:0;'),
                ),
                null,
                false,
                true,
            );
        $pageRenderer->expects(self::once())
            ->method('addJsFooterInlineCode')
            ->with(
                self::anything(),
                self::logicalAnd(
                    self::stringContains('c.appendChild(b);'),
                    self::logicalNot(self::stringContains('c.insertBefore(b,c.firstChild);')),
                ),
                null,
                false,
                true,
            );

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    private function mockExtensionConfiguration(bool $enabled): void
    {
        $mock = $this->createMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturn($enabled);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $mock);
    }

    private function buildEvent(): ModifyPageLayoutOnLoginProviderSelectionEvent
    {
        // The listener never reads the event; instantiate without the
        // constructor so the test is independent of the event's signature,
        // which differs between TYPO3 v13 and v14.
        return (new ReflectionClass(ModifyPageLayoutOnLoginProviderSelectionEvent::class))->newInstanceWithoutConstructor();
    }
}
