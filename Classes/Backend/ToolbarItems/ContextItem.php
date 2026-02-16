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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Toolbar;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ColorUtility, GeneralHelper, ViewFactoryHelper};
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;

/**
 * ContextItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextItem implements ToolbarItemInterface
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
            || !GeneralHelper::isCurrentIndicator(Toolbar::class)) {
            return '';
        }

        if (true !== (bool) ($extensionConfig['backend']['contextProduction'] ?? false) && 'Production' === Environment::getContext()->__toString()) {
            return '';
        }

        $toolbarConfig = $this->getBackendToolbarConfiguration();
        if ([] === $toolbarConfig) {
            return '';
        }

        $contextName = Environment::getContext()->__toString();
        $defaultColor = 'transparent';
        $contextColor = $toolbarConfig['color'] ?? $defaultColor;

        return ViewFactoryHelper::renderView(
            template: 'ToolbarItems/ContextItem.html',
            values: [
                'context' => [
                    'icon' => $toolbarConfig['icon']['context'] ?? 'information-application-context',
                    'text' => $toolbarConfig['text'] ?? $contextName,
                    'color' => $contextColor,
                    'textColor' => ColorUtility::getOptimalTextColor($contextColor),
                ],
            ],
        );
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
        $toolbarConfig = $this->getBackendToolbarConfiguration();

        return [] !== $toolbarConfig ? $toolbarConfig['index'] : 0;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getBackendToolbarConfiguration(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[Toolbar::class] ?? [];
    }
}
