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

namespace KonradMichalik\Typo3EnvironmentIndicator\Image\Factory;

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;

/**
 * ModifierFactoryInterface.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
interface ModifierFactoryInterface
{
    /**
     * Creates a modifier instance by type and configuration.
     *
     * @param string               $type          The modifier type
     * @param array<string, mixed> $configuration The modifier configuration
     *
     * @throws InvalidArgumentException If the modifier type is not supported or configuration is invalid
     */
    public function createModifier(string $type, array $configuration): ModifierInterface;

    /**
     * Returns the list of supported modifier types.
     *
     * @return array<string> Array of supported modifier type names
     */
    public function getSupportedModifierTypes(): array;

    /**
     * Validates configuration for a specific modifier type.
     *
     * @param string               $type          The modifier type
     * @param array<string, mixed> $configuration The configuration to validate
     *
     * @return bool True if configuration is valid, false otherwise
     */
    public function validateConfiguration(string $type, array $configuration): bool;
}
