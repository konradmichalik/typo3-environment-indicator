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
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{CircleModifier, ColorizeModifier, FrameModifier, ModifierInterface, OverlayModifier, ReplaceModifier, TextModifier, TriangleModifier};
use ReflectionClass;
use Throwable;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function sprintf;

/**
 * ModifierFactory.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ModifierFactory implements ModifierFactoryInterface
{
    private const MODIFIER_MAP = [
        'circle' => CircleModifier::class,
        'colorize' => ColorizeModifier::class,
        'frame' => FrameModifier::class,
        'overlay' => OverlayModifier::class,
        'replace' => ReplaceModifier::class,
        'text' => TextModifier::class,
        'triangle' => TriangleModifier::class,
    ];

    /**
     * @param array<string, mixed> $configuration
     */
    public function createModifier(string $type, array $configuration): ModifierInterface
    {
        if (!isset(self::MODIFIER_MAP[$type])) {
            throw new InvalidArgumentException(sprintf('Unsupported modifier type: %s. Supported types: %s', $type, implode(', ', array_keys(self::MODIFIER_MAP))), 1726357771);
        }

        if (!$this->validateConfiguration($type, $configuration)) {
            throw new InvalidArgumentException(sprintf('Invalid configuration for modifier type: %s', $type), 1726357772);
        }

        $modifierClass = self::MODIFIER_MAP[$type];

        try {
            return GeneralUtility::makeInstance($modifierClass, $configuration);
        } catch (Throwable $e) {
            throw new InvalidArgumentException(sprintf('Failed to create modifier of type: %s. Error: %s', $type, $e->getMessage()), 1726357773, $e);
        }
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedModifierTypes(): array
    {
        return array_keys(self::MODIFIER_MAP);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validateConfiguration(string $type, array $configuration): bool
    {
        if (!isset(self::MODIFIER_MAP[$type])) {
            return false;
        }

        try {
            $modifierClass = self::MODIFIER_MAP[$type];

            $reflectionClass = new ReflectionClass($modifierClass);

            return $reflectionClass->newInstanceWithoutConstructor()->validateConfiguration($configuration);
        } catch (Throwable) {
            return false;
        }
    }
}
