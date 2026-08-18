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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Utility;

use KonradMichalik\Typo3EnvironmentIndicator\Utility\ViewFactoryHelper;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ViewFactoryHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ViewFactoryHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    public function testRenderViewRendersTemplateWithAssignedValues(): void
    {
        $result = ViewFactoryHelper::renderView(
            template: 'ToolbarItems/TopbarItem.html',
            values: [
                'color' => '#bd593a',
                'textColor' => '#ffffff',
                'subTextColor' => '#eeeeee',
            ],
        );

        self::assertStringContainsString('background-color: #bd593a;', $result);
        self::assertStringContainsString('color: #ffffff;', $result);
        self::assertStringContainsString('color: #eeeeee;', $result);
    }

    public function testRenderViewResolvesExtensionPathTemplate(): void
    {
        $result = ViewFactoryHelper::renderView(
            template: 'EXT:typo3_environment_indicator/Resources/Private/Templates/ToolbarItems/TopbarItem.html',
            values: [
                'color' => '#bd593a',
                'textColor' => '#ffffff',
                'subTextColor' => '#eeeeee',
            ],
        );

        self::assertStringContainsString('background-color: #bd593a;', $result);
    }
}
