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

namespace KonradMichalik\Typo3EnvironmentIndicator\Image\Factory;

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Logo;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Image;
use KonradMichalik\Typo3EnvironmentIndicator\Image\{BackendLogoHandler, FaviconHandler, FrontendImageHandler};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function sprintf;

/**
 * ImageHandlerFactory.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageHandlerFactory implements ImageHandlerFactoryInterface
{
    private const HANDLER_TYPES = [
        'backend_logo' => BackendLogoHandler::class,
        'favicon' => FaviconHandler::class,
        'frontend_image' => FrontendImageHandler::class,
    ];

    public function createBackendLogoHandler(): BackendLogoHandler
    {
        $indicator = GeneralUtility::makeInstance(Logo::class);

        return new BackendLogoHandler($indicator);
    }

    public function createFaviconHandler(): FaviconHandler
    {
        $indicator = GeneralUtility::makeInstance(Favicon::class);

        return new FaviconHandler($indicator);
    }

    public function createFrontendImageHandler(): FrontendImageHandler
    {
        $indicator = GeneralUtility::makeInstance(Image::class);

        return new FrontendImageHandler($indicator);
    }

    public function createHandler(string $type): BackendLogoHandler|FaviconHandler|FrontendImageHandler
    {
        return match ($type) {
            'backend_logo' => $this->createBackendLogoHandler(),
            'favicon' => $this->createFaviconHandler(),
            'frontend_image' => $this->createFrontendImageHandler(),
            default => throw new InvalidArgumentException(sprintf('Unsupported handler type: %s. Supported types: %s', $type, implode(', ', array_keys(self::HANDLER_TYPES))), 1726357770),
        };
    }

    public function getSupportedHandlerTypes(): array
    {
        return array_keys(self::HANDLER_TYPES);
    }
}
