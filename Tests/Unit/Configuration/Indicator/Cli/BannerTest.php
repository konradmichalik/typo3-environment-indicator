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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Cli;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Cli\Banner;
use PHPUnit\Framework\TestCase;

/**
 * BannerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BannerTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $indicator = new Banner();
        self::assertInstanceOf(Banner::class, $indicator);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['text' => 'STAGING', 'color' => 'cyan'];
        $indicator = new Banner($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = ['text' => 'DEV', 'commands' => ['cache:*']];
        $indicator = new Banner($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        self::assertInstanceOf(AbstractIndicator::class, new Banner());
    }

    public function testImplementsIndicatorInterface(): void
    {
        self::assertInstanceOf(IndicatorInterface::class, new Banner());
    }
}
