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

namespace KonradMichalik\Typo3EnvironmentIndicator\Utility;

use KonradMichalik\Color\Color;

/**
 * ColorUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorUtility
{
    public static function getOptimalTextColor(string $color, float $opacity = 1, string $fallbackColor = '#000000'): string
    {
        $background = Color::tryFromString($color) ?? Color::fromString($fallbackColor);

        return $background->optimalTextColor()->toRgbaString($opacity);
    }
}
