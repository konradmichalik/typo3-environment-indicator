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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

use function in_array;

/**
 * Theme.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Theme implements TriggerInterface
{
    /**
     * Applies when a user never touched the theme switch.
     *
     * Matches the core default in \TYPO3\CMS\Backend\Controller\PageRenderer.
     */
    protected const DEFAULT_THEME = 'fresh';

    /**
     * @var array<int, string>
     */
    protected array $themes;

    public function __construct(string ...$theme)
    {
        $this->themes = array_values($theme);
    }

    public function check(): bool
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (!$backendUser instanceof BackendUserAuthentication) {
            return false;
        }

        $currentTheme = $backendUser->uc['theme'] ?? self::DEFAULT_THEME;

        return in_array($currentTheme, $this->themes, true);
    }
}
