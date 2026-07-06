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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{Backend, Favicon, Frontend, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{ModifierInterface, TextModifier};
use Psr\Log\{LoggerInterface, NullLogger};
use Throwable;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

use function is_string;

/**
 * IndicatorResolver.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class IndicatorResolver
{
    private const COLOR_PATTERN = '/^#([A-Fa-f0-9]{3}){1,2}$/';

    /**
     * Indicator classes whose configuration carries a plain 'color' key.
     */
    private const COLOR_TARGETS = [
        Frontend\Hint::class,
        Backend\Toolbar::class,
        Backend\Topbar::class,
        Backend\Widget::class,
        Backend\Theme::class,
    ];

    /**
     * Indicator classes whose configuration carries a plain 'text' key.
     */
    private const TEXT_TARGETS = [
        Backend\Toolbar::class,
        Backend\Widget::class,
    ];

    /**
     * Image-based indicator classes whose configuration is a modifier list.
     */
    private const IMAGE_TARGETS = [
        Favicon::class,
        Backend\Logo::class,
    ];

    public function __construct(private readonly ConfigurationStorage $configurationStorage, private readonly TriggerEvaluator $triggerEvaluator, private readonly LoggerInterface $logger = new NullLogger(), private readonly ExtensionConfiguration $extensionConfiguration = new ExtensionConfiguration()) {}

    /**
     * Resolves all active indicators based on current configuration and triggers.
     *
     * @return array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> Array of resolved indicators
     */
    public function resolveIndicators(): array
    {
        if ($this->configurationStorage->isResolved()) {
            return $this->configurationStorage->getCurrentIndicators();
        }

        $configurations = $this->configurationStorage->getConfigurations();
        foreach ($configurations as $configuration) {
            $this->processConfiguration($configuration);
        }

        $this->applyInstanceOverride();
        $this->configurationStorage->markResolved();

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

    /**
     * Applies the per-instance label/color override from the extension
     * configuration on top of all resolved indicators. Runs last on purpose:
     * instance settings win over presets and programmatic registrations.
     * Activation stays trigger-driven — indicators that did not resolve are
     * never switched on here.
     */
    protected function applyInstanceOverride(): void
    {
        $label = $this->getInstanceSetting('label');
        $color = $this->validateInstanceColor($this->getInstanceSetting('color'));

        if ('' === $label && '' === $color) {
            return;
        }

        $current = $this->configurationStorage->getCurrentIndicators();
        if ([] === $current) {
            return;
        }

        if ('' !== $color) {
            $this->overrideColors($color, $current);
        }

        if ('' !== $label) {
            $this->overrideTexts($label, $current);
            $this->overrideImages($label, '' !== $color ? $color : $this->deriveColorFromCurrent($current), $current);
        }
    }

    /**
     * @param array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> $current
     */
    private function overrideColors(string $color, array $current): void
    {
        foreach (self::COLOR_TARGETS as $indicatorClass) {
            if (isset($current[$indicatorClass])) {
                $this->configurationStorage->mergeCurrentIndicator($indicatorClass, ['color' => $color]);
            }
        }
    }

    /**
     * @param array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> $current
     */
    private function overrideTexts(string $label, array $current): void
    {
        foreach (self::TEXT_TARGETS as $indicatorClass) {
            if (isset($current[$indicatorClass])) {
                $this->configurationStorage->mergeCurrentIndicator($indicatorClass, ['text' => $label]);
            }
        }
    }

    /**
     * @param array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> $current
     */
    private function overrideImages(string $label, string $color, array $current): void
    {
        foreach (self::IMAGE_TARGETS as $indicatorClass) {
            if (isset($current[$indicatorClass])) {
                $this->configurationStorage->setCurrentIndicator($indicatorClass, [
                    new TextModifier([
                        'text' => $label,
                        'color' => $color,
                        'stroke' => [
                            'color' => '#ffffff',
                            'width' => 3,
                        ],
                    ]),
                ]);
            }
        }
    }

    private function validateInstanceColor(string $color): string
    {
        if ('' !== $color && 1 !== preg_match(self::COLOR_PATTERN, $color)) {
            $this->logger->warning('Ignoring invalid instance color "{color}" from extension configuration.', ['color' => $color]);

            return '';
        }

        return $color;
    }

    private function getInstanceSetting(string $key): string
    {
        try {
            $value = $this->extensionConfiguration->get(Configuration::EXT_KEY)['instance'][$key] ?? '';
        } catch (Throwable) {
            return '';
        }

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Falls back to the color of an active plain-color indicator so an image
     * override without an explicit instance color keeps the context color.
     *
     * @param array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>> $current
     */
    private function deriveColorFromCurrent(array $current): string
    {
        foreach (self::COLOR_TARGETS as $indicatorClass) {
            $candidate = $current[$indicatorClass]['color'] ?? null;
            if (is_string($candidate) && 1 === preg_match(self::COLOR_PATTERN, $candidate)) {
                return $candidate;
            }
        }

        return '#bd593a';
    }
}
