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

use KonradMichalik\Typo3EnvironmentIndicator\Utility\ColorUtility;
use PHPUnit\Framework\TestCase;

/**
 * ColorUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorUtilityTest extends TestCase
{
    public function testGetOptimalTextColorForLightColor(): void
    {
        self::assertSame('rgba(0, 0, 0, 1)', ColorUtility::getOptimalTextColor('#ffffff'));
    }

    public function testGetOptimalTextColorForDarkColor(): void
    {
        self::assertSame('rgba(255, 255, 255, 1)', ColorUtility::getOptimalTextColor('#000000'));
    }

    public function testGetOptimalTextColorWithOpacity(): void
    {
        self::assertSame('rgba(0, 0, 0, 0.5)', ColorUtility::getOptimalTextColor('#ffffff', 0.5));
    }

    public function testGetOptimalTextColorFallsBackForUnparseableColor(): void
    {
        self::assertSame('rgba(255, 255, 255, 1)', ColorUtility::getOptimalTextColor('transparent'));
    }

    public function testGetOptimalTextColorUsesCustomFallbackColor(): void
    {
        self::assertSame('rgba(0, 0, 0, 1)', ColorUtility::getOptimalTextColor('transparent', fallbackColor: '#ffffff'));
    }
}
