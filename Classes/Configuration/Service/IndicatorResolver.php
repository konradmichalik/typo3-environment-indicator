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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use Psr\Log\{LoggerInterface, NullLogger};
use Throwable;

/**
 * IndicatorResolver.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class IndicatorResolver
{
    public function __construct(private readonly ConfigurationStorage $configurationStorage, private readonly TriggerEvaluator $triggerEvaluator, private readonly LoggerInterface $logger = new NullLogger()) {}

    /**
     * Resolves all active indicators based on current configuration and triggers.
     *
     * @return array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> Array of resolved indicators
     */
    public function resolveIndicators(): array
    {
        if ($this->configurationStorage->hasCurrentIndicators()) {
            return $this->configurationStorage->getCurrentIndicators();
        }

        $configurations = $this->configurationStorage->getConfigurations();
        foreach ($configurations as $configuration) {
            $this->processConfiguration($configuration);
        }

        return $this->configurationStorage->getCurrentIndicators();
    }

    /**
     * Validates that indicators are properly configured.
     *
     * @param array<int, mixed> $indicators Array of potential indicator objects
     *
     * @return bool True if all indicators are valid, false otherwise
     */
    public function validateIndicators(array $indicators): bool
    {
        foreach ($indicators as $indicator) {
            if (!$indicator instanceof IndicatorInterface) {
                return false;
            }
        }

        return true;
    }

    /**
     * Processes a single configuration entry.
     *
     * @param array<string, mixed> $configuration The configuration to process
     */
    protected function processConfiguration(array $configuration): void
    {
        $triggers = $configuration['triggers'] ?? [];
        $indicators = $configuration['indicators'] ?? [];

        if ([] === $indicators) {
            return;
        }

        if (!$this->triggerEvaluator->evaluateTriggers($triggers)) {
            return;
        }

        foreach ($indicators as $indicator) {
            $this->processIndicator($indicator);
        }
    }

    /**
     * Processes a single indicator.
     *
     * @param IndicatorInterface $indicator The indicator to process
     */
    protected function processIndicator(IndicatorInterface $indicator): void
    {
        try {
            $indicatorClass = $indicator::class;
            $configuration = $indicator->getConfiguration();

            // Merge with existing configuration or set new one
            $this->configurationStorage->mergeCurrentIndicator($indicatorClass, $configuration);
        } catch (Throwable $e) {
            $this->logger->warning('Indicator processing failed: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        }
    }
}
