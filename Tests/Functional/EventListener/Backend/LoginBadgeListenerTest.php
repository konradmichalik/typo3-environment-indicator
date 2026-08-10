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

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Backend\LoginBadgeListener;
use ReflectionClass;
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * LoginBadgeListenerTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class LoginBadgeListenerTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['login' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Login::class => ['color' => '#00ACC1']],
                'resolved' => true,
            ]],
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testBadgeFallsBackToApplicationContext(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsFooterInlineCode')
            ->with(
                Configuration::EXT_KEY.'_login',
                self::callback(static fn (string $js): bool => str_contains($js, 'Testing')),
                true,
                false,
                true,
            );

        // The listener never reads the event; instantiate without the
        // constructor so the test is independent of the event's signature,
        // which differs between TYPO3 v13 and v14.
        $event = (new ReflectionClass(ModifyPageLayoutOnLoginProviderSelectionEvent::class))->newInstanceWithoutConstructor();

        (new LoginBadgeListener($pageRenderer))($event);
    }
}
