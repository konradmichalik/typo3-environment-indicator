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

use Intervention\Image\Interfaces\DriverInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use RuntimeException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ImageDriverUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageDriverUtility
{
    public const IMAGE_DRIVER_GD = 'gd';
    public const IMAGE_DRIVER_IMAGICK = 'imagick';
    public const IMAGE_DRIVER_VIPS = 'vips';

    public static function getImageDriverConfiguration(): string
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(Configuration::EXT_KEY)['general']['imageDriver'] ?? self::IMAGE_DRIVER_GD;
    }

    public static function resolveDriver(): DriverInterface
    {
        switch (self::getImageDriverConfiguration()) {
            case self::IMAGE_DRIVER_IMAGICK:
                return new \Intervention\Image\Drivers\Imagick\Driver();
            case self::IMAGE_DRIVER_VIPS:
                if (!class_exists('\\Intervention\\Image\\Drivers\\Vips\\Driver')) {
                    throw new RuntimeException('Vips intervention image driver not available, you need intervention/image-driver-vips', 1741785476);
                }

                // @codeCoverageIgnoreStart
                // Only reachable with the optional intervention/image-driver-vips
                // package installed; not part of this project's test environment.
                return new \Intervention\Image\Drivers\Vips\Driver(); // @phpstan-ignore-line
                // @codeCoverageIgnoreEnd
            case self::IMAGE_DRIVER_GD:
            default:
                return new \Intervention\Image\Drivers\Gd\Driver();
        }
    }
}
