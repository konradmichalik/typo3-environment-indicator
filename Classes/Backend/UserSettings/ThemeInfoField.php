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

namespace KonradMichalik\Typo3EnvironmentIndicator\Backend\UserSettings;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ThemeInfoField.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeInfoField
{
    private const MINIMUM_TYPO3_VERSION = 14;

    /**
     * Renders an info box in User Settings when the Theme indicator is active.
     *
     * @param array<string, mixed> $config
     */
    public function render(array &$config, object $parentObject): string
    {
        if (!$this->isApplicable()) {
            return '';
        }

        $message = $this->getLanguageService()->sL(
            'LLL:EXT:typo3_environment_indicator/Resources/Private/Language/locallang.xlf:userSettings.themeInfo',
        );

        return '<div class="alert alert-info" role="alert">'
            .'<div class="alert-body">'
            .htmlspecialchars($message, \ENT_QUOTES)
            .'</div>'
            .'</div>';
    }

    private function isApplicable(): bool
    {
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
        if ($typo3Version->getMajorVersion() < self::MINIMUM_TYPO3_VERSION) {
            return false;
        }

        $extensionConfig = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get(Configuration::EXT_KEY);

        if (true !== (bool) ($extensionConfig['backend']['theme'] ?? false)) {
            return false;
        }

        return GeneralHelper::isCurrentIndicator(Theme::class);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
