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

namespace KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ColorUtility, GeneralHelper, ViewFactoryHelper};
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function sprintf;

/**
 * TopbarItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TopbarItem implements ToolbarItemInterface
{
    public function __construct(
        protected readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function checkAccess(): bool
    {
        return true;
    }

    public function getItem(): string
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);
        if (true !== (bool) ($extensionConfig['backend']['context'] ?? false)
            || !GeneralHelper::isCurrentIndicator(Topbar::class)) {
            return '';
        }

        if (true !== (bool) ($extensionConfig['backend']['contextProduction'] ?? false) && 'Production' === Environment::getContext()->__toString()) {
            return '';
        }

        $color = $this->getBackendTopbarConfiguration()['color'] ?? [];

        if ([] === $color) {
            return '';
        }

        $relativeCssPath = 'typo3temp/assets/css/'.Configuration::EXT_KEY.'/';
        $absoluteCssPath = Environment::getPublicPath().'/'.$relativeCssPath;
        if (!file_exists($absoluteCssPath)) {
            GeneralUtility::mkdir_deep($absoluteCssPath);
        }

        $cssFileName = sprintf(
            'backend-%s.css',
            hash('sha256', implode('_', [Environment::getContext()->__toString(), $color])),
        );
        $absoluteCssFile = $absoluteCssPath.$cssFileName;

        if (!file_exists($absoluteCssFile)) {
            $textColor = ColorUtility::getOptimalTextColor($color);
            $subTextColor = ColorUtility::getOptimalTextColor($color, 0.8);

            $fileContent = ViewFactoryHelper::renderView(
                template: 'ToolbarItems/TopbarItem.html',
                values: [
                    'color' => $color,
                    'textColor' => $textColor,
                    'subTextColor' => $subTextColor,
                ],
            );

            GeneralUtility::writeFile($absoluteCssFile, $fileContent);
        }

        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile($relativeCssPath.$cssFileName);

        return '';
    }

    public function hasDropDown(): bool
    {
        return false;
    }

    public function getDropDown(): string
    {
        return '';
    }

    /**
     * @return array<string, string>
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    public function getIndex(): int
    {
        return 0;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getBackendTopbarConfiguration(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[Topbar::class] ?? [];
    }
}
