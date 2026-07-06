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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;

/**
 * ConfigurationStorage.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationStorage
{
    /**
     * Adds a configuration entry to the global configuration.
     *
     * @param array<string, mixed> $configuration The configuration array to add
     */
    public function addConfiguration(array $configuration): void
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [];
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'][] = $configuration;
    }

    /**
     * Retrieves all registered configurations.
     *
     * @return array<int, array<string, mixed>> Array of configuration arrays
     */
    public function getConfigurations(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] ?? [];
    }

    /**
     * Retrieves the current resolved indicators.
     *
     * @return array<class-string<Configuration\Indicator\IndicatorInterface>, array<string|int, mixed|ModifierInterface>> Current indicators array
     */
    public function getCurrentIndicators(): array
    {
        /* @var array<class-string<\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface>, array<string|int, mixed|ModifierInterface>> */
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] ?? [];
    }

    /**
     * Checks if any current indicators have been resolved for this request.
     *
     * @return bool True if there is at least one resolved indicator, false otherwise
     */
    public function hasCurrentIndicators(): bool
    {
        return ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] ?? []) !== [];
    }

    /**
     * Checks whether indicator resolution has already run for this request.
     *
     * Unlike {@see hasCurrentIndicators()} this also returns true when
     * resolution produced no matching indicators (e.g. on production), so the
     * result can be memoized instead of being recomputed on every call.
     */
    public function isResolved(): bool
    {
        return true === ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] ?? false);
    }

    /**
     * Marks indicator resolution as completed for this request.
     */
    public function markResolved(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    /**
     * Sets an indicator configuration for the current request.
     *
     * @param string                                     $indicatorClass The indicator class name
     * @param array<string|int, mixed|ModifierInterface> $configuration  The indicator configuration
     */
    public function setCurrentIndicator(string $indicatorClass, array $configuration): void
    {
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'][$indicatorClass] = $configuration;
    }

    /**
     * Merges an indicator configuration with existing one.
     *
     * @param string                                     $indicatorClass The indicator class name
     * @param array<string|int, mixed|ModifierInterface> $configuration  The indicator configuration to merge
     */
    public function mergeCurrentIndicator(string $indicatorClass, array $configuration): void
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'][$indicatorClass])) {
            $existing = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'][$indicatorClass];
            $merged = array_replace_recursive($existing, $configuration);
            $this->setCurrentIndicator($indicatorClass, $merged);
        } else {
            $this->setCurrentIndicator($indicatorClass, $configuration);
        }
    }

    /**
     * Checks if a configuration storage exists.
     *
     * @return bool True if configuration storage exists, false otherwise
     */
    public function hasConfigurations(): bool
    {
        return isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']);
    }
}
