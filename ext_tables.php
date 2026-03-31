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

use KonradMichalik\Typo3EnvironmentIndicator\Backend\UserSettings\ThemeInfoField;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\{ExtensionManagementUtility, GeneralUtility};

defined('TYPO3') || exit;

if (GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= 14
    && !method_exists(ExtensionManagementUtility::class, 'addUserSetting')
) {
    $GLOBALS['TYPO3_USER_SETTINGS']['columns']['environmentIndicatorThemeInfo'] = [
        'type' => 'user',
        'userFunc' => ThemeInfoField::class.'->render',
        'label' => '',
    ];

    ExtensionManagementUtility::addFieldsToUserSettings(
        'environmentIndicatorThemeInfo',
        'after:theme',
    );
}
