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

use Intervention\Image\Geometry\Factories\CircleFactory;
use Intervention\Image\Interfaces\ImageInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageManagerHelper;

use function in_array;
use function is_string;

/**
 * CircleModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class CircleModifier extends AbstractModifier implements ModifierInterface
{
    public function modify(ImageInterface &$image): void
    {
        $width = $image->width();
        $height = $image->height();

        $circleSize = (int) ($width * $this->configuration['size']);
        $radius = (int) ($circleSize / 2);

        $paddingPercent = $this->configuration['padding'];
        $paddingX = (int) ($width * $paddingPercent);
        $paddingY = (int) ($height * $paddingPercent);

        $x = $width - $radius - $paddingX;
        $y = $height - $radius - $paddingY;

        switch ($this->configuration['position']) {
            case 'top left':
                $x = $radius + $paddingX;
                $y = $radius + $paddingY;
                break;
            case 'top right':
                $x = $width - $radius - $paddingX;
                $y = $radius + $paddingY;
                break;
            case 'bottom left':
                $x = $radius + $paddingX;
                $y = $height - $radius - $paddingY;
                break;
            case 'bottom right':
            default:
                break;
        }

        if (ImageManagerHelper::isVersion4()) {
            $image->drawCircle( // @phpstan-ignore arguments.count
                function (CircleFactory $circle) use ($x, $y, $radius): void { // @phpstan-ignore argument.type
                    $circle->at($x, $y); // @phpstan-ignore method.notFound
                    $circle->diameter($radius * 2);
                    $circle->background($this->configuration['color']);
                },
            );
        } else {
            $image->drawCircle(
                $x,
                $y,
                function (CircleFactory $circle) use ($radius): void {
                    $circle->radius($radius);
                    $circle->background($this->configuration['color']);
                },
            );
        }
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array{valid: bool, errors: array<int, string>}
     */
    public function validateConfigurationWithErrors(array $configuration): array
    {
        $errors = [];

        $errors = array_merge($errors, $this->validateColor($configuration));
        $errors = array_merge($errors, $this->validateSize($configuration));
        $errors = array_merge($errors, $this->validatePadding($configuration));
        $errors = array_merge($errors, $this->validatePosition($configuration));

        return [
            'valid' => [] === $errors,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<int, string>
     */
    private function validateColor(array $configuration): array
    {
        $errors = [];

        if (!isset($configuration['color'])) {
            $errors[] = 'Missing required configuration key: color';
        } elseif (!is_string($configuration['color'])) {
            $errors[] = 'Configuration key "color" must be a string';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<int, string>
     */
    private function validateSize(array $configuration): array
    {
        $errors = [];

        if (!isset($configuration['size'])) {
            $errors[] = 'Missing required configuration key: size';
        } elseif (!is_numeric($configuration['size'])) {
            $errors[] = 'Configuration key "size" must be numeric';
        } elseif ($configuration['size'] < 0 || $configuration['size'] > 1) {
            $errors[] = 'Configuration key "size" must be between 0 and 1';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<int, string>
     */
    private function validatePadding(array $configuration): array
    {
        $errors = [];

        if (!isset($configuration['padding'])) {
            $errors[] = 'Missing required configuration key: padding';
        } elseif (!is_numeric($configuration['padding'])) {
            $errors[] = 'Configuration key "padding" must be numeric';
        } elseif ($configuration['padding'] < 0 || $configuration['padding'] > 1) {
            $errors[] = 'Configuration key "padding" must be between 0 and 1';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array<int, string>
     */
    private function validatePosition(array $configuration): array
    {
        $errors = [];

        if (!isset($configuration['position'])) {
            $errors[] = 'Missing required configuration key: position';
        } elseif (!is_string($configuration['position'])) {
            $errors[] = 'Configuration key "position" must be a string';
        } else {
            $validPositions = ['top left', 'top center', 'top right', 'center left', 'center', 'center right', 'bottom left', 'bottom center', 'bottom right'];
            if (!in_array($configuration['position'], $validPositions, true)) {
                $errors[] = 'Configuration key "position" must be one of: '.implode(', ', $validPositions);
            }
        }

        return $errors;
    }
}
