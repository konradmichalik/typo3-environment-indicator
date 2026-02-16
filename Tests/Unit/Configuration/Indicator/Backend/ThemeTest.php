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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Backend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use PHPUnit\Framework\TestCase;

/**
 * ThemeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $theme = new Theme();
        self::assertInstanceOf(Theme::class, $theme);
    }

    public function testExtendsAbstractIndicator(): void
    {
        $theme = new Theme();
        self::assertInstanceOf(AbstractIndicator::class, $theme);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $theme = new Theme();
        self::assertInstanceOf(IndicatorInterface::class, $theme);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = [
            'color' => '#bd593a',
            'scaffoldHeader' => true,
            'scaffoldSidebar' => false,
            'neutralMix' => '20%',
        ];
        $theme = new Theme($config);
        self::assertEquals($config, $theme->getConfiguration());
    }
}
