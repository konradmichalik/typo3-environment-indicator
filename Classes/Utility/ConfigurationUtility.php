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

namespace KonradMichalik\Typo3EnvironmentIndicator\Utility;

use Exception;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;

use function sprintf;

/**
 * ConfigurationUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationUtility
{
    /**
     * @param array<string, mixed>|null $frontendHintConfiguration
     * @param array<string, mixed>|null $backendToolbarConfiguration
     * @param array<string, mixed>|null $backendTopbarConfiguration
     * @param array<string, mixed>|null $faviconModifierConfiguration
     * @param array<string, mixed>|null $faviconModifierFrontendConfiguration
     * @param array<string, mixed>|null $faviconModifierBackendConfiguration
     * @param array<string, mixed>|null $frontendImageModifierConfiguration
     * @param array<string, mixed>|null $backendLogoModifierConfiguration
     * @param array<string, mixed>|null $globalImageModifierConfiguration
     */
    public static function configByContext(
        string $applicationContext,
        ?array $frontendHintConfiguration = [],
        ?array $backendToolbarConfiguration = [],
        ?array $backendTopbarConfiguration = [],
        ?array $faviconModifierConfiguration = [],
        ?array $faviconModifierFrontendConfiguration = [],
        ?array $faviconModifierBackendConfiguration = [],
        ?array $frontendImageModifierConfiguration = [],
        ?array $backendLogoModifierConfiguration = [],
        ?array $globalImageModifierConfiguration = [],
    ): void {
        throw new Exception(sprintf('The "%s" method is deprecated and no longer support. Use the "%s" method instead. See the documentation for the correct usage.', __METHOD__, Configuration\Handler::class.'::addIndicator'), 5404480452);
    }
}
