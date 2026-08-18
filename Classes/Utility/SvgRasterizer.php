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

use GdImage;

/**
 * SvgRasterizer.
 *
 * Alpha-safe SVG rasterization shared by callers that need a raster image
 * from an SVG source, without going through Intervention's generic SVG
 * decoding (GD cannot decode SVG at all; Imagick flattens transparency
 * onto an opaque background).
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class SvgRasterizer
{
    /**
     * @see https://github.com/meyfa/php-svg?tab=readme-ov-file#rasterizing
     * Notes from the author:
     * This feature in particular is very much work-in-progress. Many things will look wrong and rendering large images may be very slow.
     */
    public static function rasterize(string $svgPath): ?GdImage
    {
        $loader = \SVG\SVG::fromFile($svgPath);

        if (null === $loader) {
            return null;
        }

        $document = $loader->getDocument();
        $width = (int) $document->getWidth();
        $height = (int) $document->getHeight();

        // Try to extract dimensions from viewBox if width/height are not set
        if ($width <= 0 || $height <= 0) {
            $viewBox = $document->getViewBox();
            if (null !== $viewBox) {
                $width = (int) $viewBox[2];
                $height = (int) $viewBox[3];
            }
        }

        // Fallback to default size if still invalid
        if ($width <= 0 || $height <= 0) {
            $width = 64;
            $height = 64;
        }

        // meyfa/php-svg's docblock still says "resource", but GD functions
        // return GdImage objects since PHP 8.1.
        return $loader->toRasterImage($width, $height); // @phpstan-ignore-line
    }
}
