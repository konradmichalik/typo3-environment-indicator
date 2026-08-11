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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Widgets;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Widget;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ColorUtility;
use KonradMichalik\Typo3EnvironmentIndicator\Widgets\EnvironmentIndicatorWidget;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * EnvironmentIndicatorWidgetTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class EnvironmentIndicatorWidgetTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testRenderWidgetContentUsesDefaultsWhenNotConfigured(): void
    {
        $result = $this->createSubject()->renderWidgetContent();

        self::assertStringContainsString('data-context="Testing"', $result);
        self::assertStringContainsString('background-color:transparent', $result);
        self::assertStringContainsString('color:'.ColorUtility::getOptimalTextColor('transparent', fallbackColor: '#ffffff'), $result);
        self::assertStringContainsString('font-size:20px', $result);
        self::assertStringNotContainsString('environment-indicator-widget__description', $result);
    }

    public function testRenderWidgetContentUsesConfiguredValues(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Widget::class => [
                'color' => '#bd593a',
                'text' => 'Staging',
                'description' => '  Custom description  ',
                'textSize' => '32px',
                'icon' => 'actions-info',
            ]],
            'resolved' => true,
        ]]]);

        $result = $this->createSubject()->renderWidgetContent();

        self::assertStringContainsString('data-context="Staging"', $result);
        self::assertStringContainsString('background-color:#bd593a', $result);
        self::assertStringContainsString('font-size:32px', $result);
        self::assertStringContainsString('environment-indicator-widget__description">Custom description<', $result);
    }

    private function createSubject(): EnvironmentIndicatorWidget
    {
        return new EnvironmentIndicatorWidget($this->createStub(WidgetConfigurationInterface::class));
    }
}
