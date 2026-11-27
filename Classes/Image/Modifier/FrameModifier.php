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
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageManagerHelper;

/**
 * FrameModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrameModifier extends AbstractModifier implements ModifierInterface
{
    public function modify(ImageInterface &$image): void
    {
        $width = $image->width();
        $height = $image->height();

        $borderSize = $this->configuration['borderSize'] ?? 5;
        $borderColor = $this->configuration['color'];

        ImageManagerHelper::drawRectangle($image, 0, 0, $width, $height, $borderColor, $borderSize);
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @return array{valid: bool, errors: array<int, string>}
     */
    public function validateConfigurationWithErrors(array $configuration): array
    {
        $errors = $this->requireString($configuration, 'color');

        if (isset($configuration['borderSize'])
            && (!is_numeric($configuration['borderSize']) || $configuration['borderSize'] < 0)) {
            $errors[] = 'Configuration key "borderSize" must be a number of at least 0';
        }

        return self::validationResult($errors);
    }
}
