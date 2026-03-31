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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || exit;

// @phpstan-ignore function.alreadyNarrowedType (v13 compatibility: addUserSetting() was added in v14.2)
if (method_exists(ExtensionManagementUtility::class, 'addUserSetting')) {
    ExtensionManagementUtility::addUserSetting(
        'environmentIndicatorThemeInfo',
        [
            'label' => '',
            'config' => [
                'type' => 'user',
                'userFunc' => ThemeInfoField::class.'->render',
            ],
        ],
        'after:theme',
    );
}
