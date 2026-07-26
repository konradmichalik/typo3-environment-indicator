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

use function json_encode;
use function sprintf;
use function trim;

use const JSON_THROW_ON_ERROR;

/**
 * TopbarItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TopbarItem implements ToolbarItemInterface
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly PageRenderer $pageRenderer,
    ) {}

    public function checkAccess(): bool
    {
        return $this->isApplicable();
    }

    public function getItem(): string
    {
        $color = $this->getBackendTopbarConfiguration()['color'] ?? [];

        if ([] === $color) {
            return '';
        }

        $textColor = ColorUtility::getOptimalTextColor($color);
        $subTextColor = ColorUtility::getOptimalTextColor($color, 0.8);

        $cssContent = ViewFactoryHelper::renderView(
            template: 'ToolbarItems/TopbarItem.html',
            values: [
                'color' => $color,
                'textColor' => $textColor,
                'subTextColor' => $subTextColor,
            ],
        );

        $this->pageRenderer->addCssInlineBlock(Configuration::EXT_KEY.'_topbar', $cssContent);

        $description = trim((string) ($this->getBackendTopbarConfiguration()['description'] ?? ''));
        if ('' !== $description) {
            $this->pageRenderer->addJsInlineCode(
                Configuration::EXT_KEY.'_topbar',
                sprintf('document.querySelector(".topbar")?.setAttribute("title",%s);', json_encode($description, JSON_THROW_ON_ERROR)),
            );
        }

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

    private function isApplicable(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);
        if (true !== (bool) ($extensionConfig['backend']['context'] ?? false)
            || !GeneralHelper::isCurrentIndicator(Topbar::class)) {
            return false;
        }

        return true === (bool) ($extensionConfig['backend']['contextProduction'] ?? false)
            || 'Production' !== Environment::getContext()->__toString();
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getBackendTopbarConfiguration(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[Topbar::class] ?? [];
    }
}
