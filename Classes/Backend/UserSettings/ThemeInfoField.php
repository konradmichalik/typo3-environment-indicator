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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * ThemeInfoField.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeInfoField
{
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
        return GeneralHelper::isMinimumTypo3Version(14)
            && GeneralHelper::isExtensionFeatureEnabled('backend/theme')
            && GeneralHelper::isCurrentIndicator(Theme::class);
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
