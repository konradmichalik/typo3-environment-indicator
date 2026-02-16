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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;

use function sprintf;

/**
 * ThemeItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeItem implements ToolbarItemInterface
{
    private const MINIMUM_TYPO3_VERSION = 14;

    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly PageRenderer $pageRenderer,
        private readonly Typo3Version $typo3Version,
    ) {}

    public function checkAccess(): bool
    {
        return true;
    }

    public function getItem(): string
    {
        if (!$this->isApplicable()) {
            return '';
        }

        $configuration = $this->getThemeConfiguration();
        $color = $configuration['color'] ?? '';

        if ('' === $color || !$this->isValidColor($color)) {
            return '';
        }

        $scaffoldHeader = (bool) ($configuration['scaffoldHeader'] ?? true);
        $scaffoldSidebar = (bool) ($configuration['scaffoldSidebar'] ?? true);
        $neutralMix = $configuration['neutralMix'] ?? '15%';

        if (!$this->isValidNeutralMix($neutralMix)) {
            $neutralMix = '15%';
        }

        $cssContent = $this->generateCss($color, $scaffoldHeader, $scaffoldSidebar, $neutralMix);
        $this->pageRenderer->addCssInlineBlock(Configuration::EXT_KEY.'_theme', $cssContent);

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
        if ($this->typo3Version->getMajorVersion() < self::MINIMUM_TYPO3_VERSION) {
            return false;
        }

        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        if (true !== (bool) ($extensionConfig['backend']['theme'] ?? false)) {
            return false;
        }

        return GeneralHelper::isCurrentIndicator(Theme::class);
    }

    private function generateCss(string $color, bool $scaffoldHeader, bool $scaffoldSidebar, string $neutralMix): string
    {
        $rootVars = [
            sprintf('--token-color-primary-base: %s;', $color),
            sprintf('--typo3-color-neutral-mix: %s;', $neutralMix),
        ];

        if ($scaffoldHeader) {
            $rootVars[] = '--typo3-scaffold-header-color: var(--typo3-surface-primary-text);';
            $rootVars[] = '--typo3-scaffold-header-bg: light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%));';
            $rootVars[] = '--typo3-scaffold-header-box-shadow: none;';
        }

        if ($scaffoldSidebar) {
            $rootVars[] = '--typo3-scaffold-sidebar-color: var(--typo3-surface-primary-text);';
            $rootVars[] = '--typo3-scaffold-sidebar-bg: light-dark(hsl(from var(--token-color-primary-base) h 40% 20%), hsl(from var(--token-color-primary-base) h 20% 10%));';
            $rootVars[] = '--typo3-scaffold-sidebar-border-width: 0;';
        }

        $css = ':root {'."\n    ".implode("\n    ", $rootVars)."\n}\n";

        if ($scaffoldSidebar) {
            $css .= '[data-theme] .scaffold-sidebar {'."\n";
            $css .= '    --typo3-icons-accent: light-dark(hsl(from var(--token-color-primary-base) h 80% 70%), hsl(from var(--token-color-primary-base) h 60% 60%));'."\n";
            $css .= '}'."\n";
        }

        return $css;
    }

    private function isValidColor(string $color): bool
    {
        return 1 === preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color);
    }

    private function isValidNeutralMix(string $mix): bool
    {
        return 1 === preg_match('/^\d+(\.\d+)?%$/', $mix);
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getThemeConfiguration(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[Theme::class] ?? [];
    }
}
