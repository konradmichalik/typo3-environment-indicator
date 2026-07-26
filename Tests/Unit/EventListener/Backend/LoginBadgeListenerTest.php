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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Backend\LoginBadgeListener;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewInterface;

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
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        GeneralUtility::purgeInstances();
    }

    public function testNothingIsInjectedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    public function testNothingIsInjectedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([]);
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    public function testBadgeIsInjectedWithTextAndDescription(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([
            Login::class => ['text' => 'STAGING', 'color' => '#00ACC1', 'description' => 'DB synced nightly'],
        ]);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(Configuration::EXT_KEY.'_login', self::stringContains('#00ACC1'));
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                Configuration::EXT_KEY.'_login',
                self::callback(static fn (string $js): bool => str_contains($js, 'STAGING') && str_contains($js, 'DB synced nightly')),
            );

        (new LoginBadgeListener($pageRenderer))($this->buildEvent());
    }

    private function mockExtensionConfiguration(bool $enabled): void
    {
        $mock = $this->createMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturn($enabled);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $mock);
    }

    /**
     * @param array<class-string, array<string, mixed>> $indicators
     */
    private function setResolvedIndicators(array $indicators): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = $indicators;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    private function buildEvent(): ModifyPageLayoutOnLoginProviderSelectionEvent
    {
        return new ModifyPageLayoutOnLoginProviderSelectionEvent(
            $this->createStub(ViewInterface::class),
            $this->createStub(ServerRequestInterface::class),
        );
    }
}
