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
use KonradMichalik\Typo3EnvironmentIndicator\Image\{BackendLogoHandler, FaviconHandler, FrontendImageHandler};

/**
 * ImageHandlerFactoryInterface.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
interface ImageHandlerFactoryInterface
{
    /**
     * Creates a backend logo handler instance.
     */
    public function createBackendLogoHandler(): BackendLogoHandler;

    /**
     * Creates a favicon handler instance.
     */
    public function createFaviconHandler(): FaviconHandler;

    /**
     * Creates a frontend image handler instance.
     */
    public function createFrontendImageHandler(): FrontendImageHandler;

    /**
     * Creates a handler by type name.
     *
     * @param string $type The handler type ('backend_logo', 'favicon', 'frontend_image')
     *
     * @throws InvalidArgumentException If the handler type is not supported
     */
    public function createHandler(string $type): BackendLogoHandler|FaviconHandler|FrontendImageHandler;

    /**
     * Returns the list of supported handler types.
     *
     * @return array<string> Array of supported handler type names
     */
    public function getSupportedHandlerTypes(): array;
}
