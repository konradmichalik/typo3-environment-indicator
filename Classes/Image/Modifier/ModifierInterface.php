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

namespace KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier;

use Intervention\Image\Interfaces\ImageInterface;

/**
 * ModifierInterface.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
interface ModifierInterface
{
    public function modify(ImageInterface &$image): void;

    /**
     * Returns the resolved configuration of this modifier.
     *
     * @return array<string, mixed> The modifier configuration
     */
    public function getConfiguration(): array;

    /**
     * Validates the configuration for this modifier.
     *
     * @param array<string, mixed> $configuration The configuration to validate
     *
     * @return bool True if configuration is valid, false otherwise
     */
    public function validateConfiguration(array $configuration): bool;

    /**
     * Validates the configuration and returns detailed error information.
     *
     * @param array<string, mixed> $configuration The configuration to validate
     *
     * @return array{valid: bool, errors: array<int, string>} Array with 'valid' (bool) and 'errors' (array) keys
     */
    public function validateConfigurationWithErrors(array $configuration): array;
}
