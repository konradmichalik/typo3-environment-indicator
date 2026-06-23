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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Utility;

use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageDriverUtility;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ImageDriverUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageDriverUtilityTest extends TestCase
{
    public function testConstantsAreCorrect(): void
    {
        self::assertEquals('gd', ImageDriverUtility::IMAGE_DRIVER_GD);
        self::assertEquals('imagick', ImageDriverUtility::IMAGE_DRIVER_IMAGICK);
        self::assertEquals('vips', ImageDriverUtility::IMAGE_DRIVER_VIPS);
    }

    public function testResolveDriverMethodIsStatic(): void
    {
        $reflection = new ReflectionClass(ImageDriverUtility::class);
        $method = $reflection->getMethod('resolveDriver');
        self::assertTrue($method->isStatic());
    }

    public function testGetImageDriverConfigurationMethodIsStatic(): void
    {
        $reflection = new ReflectionClass(ImageDriverUtility::class);
        $method = $reflection->getMethod('getImageDriverConfiguration');
        self::assertTrue($method->isStatic());
    }

}
