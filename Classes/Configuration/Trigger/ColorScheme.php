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

use InvalidArgumentException;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

use function in_array;

/**
 * ColorScheme.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorScheme implements TriggerInterface
{
    /**
     * Mirrors \TYPO3\CMS\Backend\Backend\ColorScheme, which is not referenced
     * directly to keep the trigger independent of backend internals.
     */
    protected const AVAILABLE_SCHEMES = ['light', 'dark', 'auto'];

    /**
     * Applies when a user never touched the color scheme switch.
     */
    protected const DEFAULT_SCHEME = 'auto';

    /**
     * @var array<int, string>
     */
    protected array $schemes;

    public function __construct(string ...$scheme)
    {
        foreach ($scheme as $colorScheme) {
            if (!in_array($colorScheme, self::AVAILABLE_SCHEMES, true)) {
                throw new InvalidArgumentException('Invalid color scheme: '.$colorScheme.'. Allowed values are: '.implode(', ', self::AVAILABLE_SCHEMES), 1786320000);
            }
        }
        $this->schemes = array_values($scheme);
    }

    public function check(): bool
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (!$backendUser instanceof BackendUserAuthentication) {
            return false;
        }

        $currentScheme = $backendUser->uc['colorScheme'] ?? self::DEFAULT_SCHEME;

        return in_array($currentScheme, $this->schemes, true);
    }
}
