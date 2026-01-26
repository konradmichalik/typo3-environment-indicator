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

use function is_string;
use function sprintf;
use function strlen;

/**
 * ColorUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorUtility
{
    public static function getColoredString(?string $name = null): string
    {
        $name ??= (string) $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'];
        $hash = hash('sha256', $name);
        $hue = hexdec(substr($hash, 0, 2)) / 255 * 360;

        return sprintf('hsl(%d, 70%%, 60%%)', $hue);
    }

    /**
     * @param array<int, int>|string $fallbackColor
     */
    public static function getOptimalTextColor(string $color, float $opacity = 1, array|string $fallbackColor = [0, 0, 0]): string
    {
        $rgb = self::colorToRgb($color, $fallbackColor);

        return self::calculateLuminance($rgb[0], $rgb[1], $rgb[2]) > 0.5 ? "rgba(0,0,0,$opacity)" : "rgba(255,255,255,$opacity)";
    }

    /**
     * @param array<int, int>|string $fallbackColor
     *
     * @return array<int, int>
     */
    public static function colorToRgb(string $color, array|string $fallbackColor = [0, 0, 0]): array
    {
        if (1 === preg_match('/^#([a-fA-F0-9]{3,6})$/', $color, $matches)) {
            return self::hexToRgb($color);
        }
        if (1 === preg_match('/rgb\((\d+),\s*(\d+),\s*(\d+)\)/', $color, $matches)) {
            return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
        }
        if (1 === preg_match('/hsl\((\d+),\s*(\d+)%?,\s*(\d+)%?\)/', $color, $matches)) {
            $hslResult = self::hslToRgb((int) $matches[1], (int) $matches[2], (int) $matches[3]);

            return [(int) $hslResult[0], (int) $hslResult[1], (int) $hslResult[2]];
        }

        if (is_string($fallbackColor)) {
            $fallbackColor = self::colorToRgb($fallbackColor);
        }

        return $fallbackColor;
    }

    /**
     * @return array<int, int>
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (3 === strlen($hex)) {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }

        return [(int) hexdec($hex[0].$hex[1]), (int) hexdec($hex[2].$hex[3]), (int) hexdec($hex[4].$hex[5])];
    }

    /**
     * @return array<int, float|int>
     */
    public static function hslToRgb(int $h, int $s, int $l): array
    {
        $s /= 100;
        $l /= 100;
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60.0, 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            [$r, $g, $b] = [$c, $x, 0];
        } elseif ($h < 120) {
            [$r, $g, $b] = [$x, $c, 0];
        } elseif ($h < 180) {
            [$r, $g, $b] = [0, $c, $x];
        } elseif ($h < 240) {
            [$r, $g, $b] = [0, $x, $c];
        } elseif ($h < 300) {
            [$r, $g, $b] = [$x, 0, $c];
        } else {
            [$r, $g, $b] = [$c, 0, $x];
        }

        return [round(($r + $m) * 255), round(($g + $m) * 255), round(($b + $m) * 255)];
    }

    public static function calculateLuminance(int $r, int $g, int $b): float
    {
        return 0.2126 * ($r / 255) + 0.7152 * ($g / 255) + 0.0722 * ($b / 255);
    }
}
