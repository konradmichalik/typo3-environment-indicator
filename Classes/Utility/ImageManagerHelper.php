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

/**
 * ImageManagerHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageManagerHelper
{
    private const VERSION_4_READ_METHOD = 'decode';

    public static function readImage(ImageManager|ImageManagerInterface $manager, string $path): ImageInterface
    {
        // intervention/image v4 renamed read() to decode()
        if (self::isVersion4()) {
            return $manager->{self::VERSION_4_READ_METHOD}($path); // @phpstan-ignore method.dynamicName, method.notFound
        }

        return $manager->read($path);
    }

    public static function isVersion4(): bool
    {
        return method_exists(ImageManager::class, self::VERSION_4_READ_METHOD); // @phpstan-ignore function.impossibleType
    }
}
