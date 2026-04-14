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

namespace KonradMichalik\Typo3EnvironmentIndicator\Backend\Form\Element;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

/**
 * ThemeInfoElement.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeInfoElement extends AbstractFormElement
{
    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();

        if (!$this->isApplicable()) {
            return $resultArray;
        }

        $message = $this->getLanguageService()->sL(
            'LLL:EXT:typo3_environment_indicator/Resources/Private/Language/locallang.xlf:userSettings.themeInfo',
        );

        $html = '<div class="alert alert-info">'
            .'<div class="alert-body">'
            .htmlspecialchars($message, \ENT_QUOTES)
            .'</div>'
            .'</div>';

        $resultArray['html'] = $this->wrapWithFieldsetAndLegend($html);

        return $resultArray;
    }

    private function isApplicable(): bool
    {
        return GeneralHelper::isMinimumTypo3Version(14)
            && GeneralHelper::isExtensionFeatureEnabled('backend/theme')
            && GeneralHelper::isCurrentIndicator(Theme::class);
    }
}
