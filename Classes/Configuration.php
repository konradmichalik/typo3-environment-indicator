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

namespace KonradMichalik\Typo3EnvironmentIndicator;

use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\{ConsoleItem, ContextItem, PageTitleItem, ThemeItem, TopbarItem};

/**
 * Configuration.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Configuration
{
    final public const EXT_KEY = 'typo3_environment_indicator';

    final public const EXT_NAME = 'Typo3EnvironmentIndicator';

    public static function addToolbarItems(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1740392103] = ContextItem::class;
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1740392104] = TopbarItem::class;
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1740392105] = ThemeItem::class;
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1740392106] = PageTitleItem::class;
        $GLOBALS['TYPO3_CONF_VARS']['BE']['toolbarItems'][1740392107] = ConsoleItem::class;
    }
}
