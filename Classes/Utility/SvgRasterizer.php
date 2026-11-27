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
use SVG\Nodes\Embedded\SVGImage;
use SVG\Nodes\Structures\SVGDocumentFragment;
use SVG\SVG;
use TypeError;

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
    private const MAX_EDGE = 1024;

    /**
     * @see https://github.com/meyfa/php-svg?tab=readme-ov-file#rasterizing
     * Notes from the author:
     * This feature in particular is very much work-in-progress. Many things will look wrong and rendering large images may be very slow.
     */
    public static function rasterize(string $svgPath): ?GdImage
    {
        $loader = self::loadSvg($svgPath);

        if (null === $loader) {
            return null;
        }

        $document = $loader->getDocument();

        if (self::referencesExternalImage($document)) {
            return null;
        }

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

        [$width, $height] = self::clampDimensions($width, $height);

        // meyfa/php-svg's docblock still says "resource", but GD functions
        // return GdImage objects since PHP 8.1.
        return $loader->toRasterImage($width, $height);
    }

    /**
     * php-svg resolves an <image> reference with file_get_contents(), which
     * reads local files and requests remote URLs. Only embedded raster images
     * are safe to render. A data URI that holds an SVG is rejected as well:
     * php-svg rasterizes it recursively and then resolves the <image>
     * references inside it without any check.
     */
    private static function referencesExternalImage(SVGDocumentFragment $document): bool
    {
        foreach ($document->getElementsByTagName('image') as $image) {
            $href = $image instanceof SVGImage ? $image->getHref() : null;

            if (null !== $href && '' !== $href && !self::isEmbeddedRasterImage($href)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mirrors the data URI handling of php-svg's ImageRenderer, so both agree
     * on what is embedded and what is loaded from outside.
     */
    private static function isEmbeddedRasterImage(string $href): bool
    {
        $commaPosition = strpos($href, ',');
        if (!str_starts_with($href, 'data:') || false === $commaPosition) {
            return false;
        }

        $content = substr($href, $commaPosition + 1);
        if (str_contains(substr($href, 0, $commaPosition), ';base64')) {
            $content = base64_decode($content, true);
            if (false === $content) {
                return false;
            }
        }

        return !str_contains($content, '<svg') || !str_contains($content, '</svg>');
    }

    /**
     * @return array{int, int}
     */
    private static function clampDimensions(int $width, int $height): array
    {
        $longestEdge = max($width, $height);
        if ($longestEdge <= self::MAX_EDGE) {
            return [$width, $height];
        }

        $scale = self::MAX_EDGE / $longestEdge;

        return [max(1, (int) round($width * $scale)), max(1, (int) round($height * $scale))];
    }

    /**
     * meyfa/php-svg's SVGReader passes simplexml_load_file()'s result straight
     * into a SimpleXMLElement-typed parameter without checking it first, so
     * malformed XML (valid XML with an unknown root element aside) surfaces as
     * an uncaught TypeError instead of the documented null return.
     */
    private static function loadSvg(string $svgPath): ?SVG
    {
        $previousSetting = libxml_use_internal_errors(true);

        try {
            return SVG::fromFile($svgPath);
        } catch (TypeError) {
            return null;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousSetting);
        }
    }
}
