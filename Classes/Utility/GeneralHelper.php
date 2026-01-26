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

use Intervention\Image\Interfaces\ImageManagerInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_key_exists;

/**
 * GeneralHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class GeneralHelper
{
    public static function getFolder(IndicatorInterface $indicator, bool $publicPath = true): string
    {
        $defaultPath = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][$indicator::class]['_path'];

        $path = Environment::getPublicPath().'/'.$defaultPath;
        if (!file_exists($path)) {
            GeneralUtility::mkdir_deep($path);
        }

        if (!$publicPath) {
            return $defaultPath;
        }

        return $path;
    }

    /**
     * @return array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>>
     */
    public static function getIndicatorConfiguration(): array
    {
        return Handler::resolveIndicators();
    }

    public static function isCurrentIndicator(string $indicatorClass): bool
    {
        return array_key_exists(
            $indicatorClass,
            self::getIndicatorConfiguration(),
        );
    }

    public static function supportFormat(ImageManagerInterface $manager, string $format): bool
    {
        return ('ico' === $format) || ('svg' === $format) || $manager->driver()->supports($format);
    }
}
