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

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;

use function sprintf;

/**
 * AbstractModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AbstractModifier
{
    /**
     * @var array<string, mixed>
     */
    protected array $configuration = [];

    /**
     * @param array<string, mixed> $configuration
     */
    public function __construct(array $configuration)
    {
        $configuration = $this->mergeGlobalConfiguration($configuration);

        $validationResult = $this->validateConfigurationWithErrors($configuration);
        if (!$validationResult['valid']) {
            throw new InvalidArgumentException(sprintf('Invalid configuration for %s: %s', static::class, implode(', ', $validationResult['errors'])), 1740401564);
        }

        $this->configuration = $configuration;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * Validates the configuration for this modifier.
     * Override this method in subclasses for custom validation logic.
     *
     * @param array<string, mixed> $configuration The configuration to validate
     *
     * @return bool True if configuration is valid, false otherwise
     */
    public function validateConfiguration(array $configuration): bool
    {
        return $this->validateConfigurationWithErrors($configuration)['valid'];
    }

    /**
     * Validates the configuration and returns detailed error information.
     * Override this method in subclasses for custom validation logic.
     *
     * @param array<string, mixed> $configuration The configuration to validate
     *
     * @return array{valid: bool, errors: array<int, string>} Array with 'valid' (bool) and 'errors' (array) keys
     */
    public function validateConfigurationWithErrors(array $configuration): array
    {
        return [
            'valid' => true,
            'errors' => [],
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<string, mixed>
     */
    protected function mergeGlobalConfiguration(array $configuration): array
    {
        /** @var array<string, mixed> $globalConfiguration */
        $globalConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][static::class] ?? [];

        return array_replace_recursive($globalConfiguration, $configuration);
    }
}
