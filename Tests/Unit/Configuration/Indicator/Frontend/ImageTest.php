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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Frontend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Image;
use PHPUnit\Framework\TestCase;

/**
 * ImageTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $image = new Image();
        self::assertInstanceOf(Image::class, $image);
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
        $image = new Image($config);
        self::assertEquals($config, $image->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = [
            'modifiers' => [
                'text' => [
                    'text' => 'DEV',
                    'color' => '#ffffff',
                    'size' => 16,
                ],
            ],
        ];
        $image = new Image($config);
        self::assertEquals($config, $image->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        $image = new Image();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator::class, $image);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $image = new Image();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface::class, $image);
    }
}
