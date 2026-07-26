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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\EventListener\Backend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Backend\LoginBadgeListener;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * LoginBadgeListenerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class LoginBadgeListenerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]['backend']['login'] = '1';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Login::class => ['color' => '#00ACC1'],
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
        );
        parent::tearDown();
    }

    public function testBadgeFallsBackToApplicationContext(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                Configuration::EXT_KEY.'_login',
                self::callback(static fn (string $js): bool => str_contains($js, 'Testing')),
            );

        $event = new ModifyPageLayoutOnLoginProviderSelectionEvent(
            $this->createStub(ViewInterface::class),
            $this->createStub(ServerRequestInterface::class),
        );

        (new LoginBadgeListener($pageRenderer))($event);
    }
}
