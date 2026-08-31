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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service\{ConfigurationStorage, IndicatorResolver, TriggerEvaluator};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handler.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Handler
{
    private static ?IndicatorResolver $indicatorResolver = null;
    private static ?ConfigurationStorage $configurationStorage = null;
    private static ?TriggerEvaluator $triggerEvaluator = null;

    /**
     * Adds a new indicator/trigger configuration.
     *
     * @param TriggerInterface[]   $triggers   Array of trigger objects
     * @param IndicatorInterface[] $indicators Array of indicator objects
     */
    public static function addIndicator(array $triggers = [], array $indicators = []): void
    {
        if (!self::validateConfiguration($triggers, $indicators)) {
            return;
        }

        $configuration = [
            'triggers' => $triggers,
            'indicators' => $indicators,
        ];

        $configurationStorage = self::getConfigurationStorage();

        if ($configurationStorage->hasConfigurations() || [] !== $triggers || [] !== $indicators) {
            $configurationStorage->addConfiguration($configuration);
        }
    }

    /**
     * Resolves the current indicators based on the registered configurations and the checked triggers.
     *
     * @return array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> Current indicators
     */
    public static function resolveIndicators(): array
    {
        return self::getIndicatorResolver()->resolveIndicators();
    }

    /**
     * Validates the configuration input.
     *
     * @param array<int, TriggerInterface>   $triggers   Array of potential trigger objects
     * @param array<int, IndicatorInterface> $indicators Array of potential indicator objects
     *
     * @return bool True if configuration is valid, false otherwise
     */
    private static function validateConfiguration(array $triggers, array $indicators): bool
    {
        if ([] === $triggers && [] === $indicators) {
            return false;
        }

        $triggerEvaluator = self::getTriggerEvaluator();
        $indicatorResolver = self::getIndicatorResolver();

        if ([] !== $triggers && !$triggerEvaluator->validateTriggers($triggers)) {
            return false;
        }

        if ([] !== $indicators && !$indicatorResolver->validateIndicators($indicators)) {
            return false;
        }

        return true;
    }

    /**
     * Gets the configuration storage service.
     */
    private static function getConfigurationStorage(): ConfigurationStorage
    {
        self::$configurationStorage ??= GeneralUtility::makeInstance(ConfigurationStorage::class);

        return self::$configurationStorage;
    }

    /**
     * Gets the trigger evaluator service.
     */
    private static function getTriggerEvaluator(): TriggerEvaluator
    {
        self::$triggerEvaluator ??= GeneralUtility::makeInstance(TriggerEvaluator::class);

        return self::$triggerEvaluator;
    }

    /**
     * Gets the indicator resolver service.
     */
    private static function getIndicatorResolver(): IndicatorResolver
    {
        if (null === self::$indicatorResolver) {
            $configurationStorage = self::getConfigurationStorage();
            $triggerEvaluator = self::getTriggerEvaluator();

            self::$indicatorResolver = GeneralUtility::makeInstance(
                IndicatorResolver::class,
                $configurationStorage,
                $triggerEvaluator,
            );
        }

        return self::$indicatorResolver;
    }
}
