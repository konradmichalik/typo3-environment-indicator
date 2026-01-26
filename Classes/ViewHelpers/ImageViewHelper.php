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

namespace KonradMichalik\Typo3EnvironmentIndicator\ViewHelpers;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Image;
use KonradMichalik\Typo3EnvironmentIndicator\Image\FrontendImageHandler;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\{GeneralUtility, PathUtility};
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ImageViewHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('_path', 'string', 'Image path');
    }

    public function render(): string
    {
        $image = $this->renderChildren();

        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);
        if (true !== (bool) ($extensionConfig['frontend']['image'] ?? false)
            || !GeneralHelper::isCurrentIndicator(Image::class)
        ) {
            return $image;
        }

        if (!PathUtility::isExtensionPath($image)) {
            $image = Environment::getPublicPath().(str_contains((string) $image, '?') ? strtok($image, '?') : $image);
        }

        return GeneralUtility::makeInstance(FrontendImageHandler::class)->process($image);
    }
}
