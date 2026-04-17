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

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\{ImageInterface, ImageManagerInterface};
use Intervention\Image\Typography\FontFactory;

/**
 * ImageManagerHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageManagerHelper
{
    public static function readImage(ImageManager|ImageManagerInterface $manager, string $path): ImageInterface
    {
        $method = self::isVersion4() ? 'decode' : 'read';

        return $manager->{$method}($path);
    }

    /**
     * v3: drawCircle($x, $y, callable) — v4: drawCircle(callable) with at()/diameter() inside callback.
     */
    public static function drawCircle(ImageInterface $image, int $x, int $y, int $radius, string $color): void
    {
        $callback = static function (object $circle) use ($x, $y, $radius, $color): void {
            if (method_exists($circle, 'at')) {
                $circle->at($x, $y);
                $circle->diameter($radius * 2);
            } else {
                $circle->radius($radius);
            }
            $circle->background($color);
        };

        if (self::isVersion4()) {
            $image->{'drawCircle'}($callback);

            return;
        }

        $image->drawCircle($x, $y, $callback);
    }

    /**
     * v3: drawRectangle($x, $y, callable) — v4: drawRectangle(callable) with at() inside callback.
     */
    public static function drawRectangle(ImageInterface $image, int $x, int $y, int $width, int $height, string $borderColor, int $borderSize): void
    {
        $callback = static function (object $rectangle) use ($x, $y, $width, $height, $borderColor, $borderSize): void {
            if (method_exists($rectangle, 'at')) {
                $rectangle->at($x, $y);
            }
            $rectangle->size($width, $height);
            $rectangle->border($borderColor, $borderSize);
        };

        if (self::isVersion4()) {
            $image->{'drawRectangle'}($callback);

            return;
        }

        $image->drawRectangle($x, $y, $callback);
    }

    /**
     * v3: place($element, $position, $offsetX, $offsetY) — v4: insert($element, $x, $y, $alignment).
     */
    public static function placeImage(ImageInterface $image, ImageInterface $overlay, string $position, int $offsetX, int $offsetY): void
    {
        if (self::isVersion4()) {
            $image->{'insert'}($overlay, $offsetX, $offsetY, $position);

            return;
        }

        $image->{'place'}($overlay, $position, $offsetX, $offsetY);
    }

    /**
     * v3: align($horizontal) + valign($vertical) — v4: align($horizontal, $vertical).
     */
    public static function setFontAlignment(FontFactory $font, string $horizontal, string $vertical): void
    {
        if (self::isVersion4()) {
            $font->{'align'}($horizontal, $vertical);

            return;
        }

        $font->align($horizontal);
        $font->{'valign'}($vertical);
    }

    public static function isVersion4(): bool
    {
        return method_exists(ImageManager::class, 'decode');
    }
}
