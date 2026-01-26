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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Logo;
use PHPUnit\Framework\TestCase;

/**
 * LogoTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class LogoTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $logo = new Logo();
        self::assertInstanceOf(Logo::class, $logo);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = [
            'modifiers' => [
                'circle' => [
                    'color' => '#ff0000',
                    'size' => 0.3,
                    'position' => 'top right',
                ],
            ],
        ];
        $logo = new Logo($config);
        self::assertEquals($config, $logo->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = [
            'modifiers' => [
                'frame' => [
                    'color' => '#00ff00',
                    'size' => 5,
                ],
            ],
        ];
        $logo = new Logo($config);
        self::assertEquals($config, $logo->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        $logo = new Logo();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator::class, $logo);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $logo = new Logo();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface::class, $logo);
    }
}
