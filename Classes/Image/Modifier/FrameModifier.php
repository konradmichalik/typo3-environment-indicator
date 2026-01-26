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

use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Interfaces\ImageInterface;

use function is_string;

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
        $borderColor = $this->configuration['color'] ?? 'black';

        $image->drawRectangle(0, 0, function (RectangleFactory $rectangle) use ($width, $height, $borderSize, $borderColor): void {
            $rectangle->size($width, $height);
            $rectangle->border($borderColor, $borderSize);
        });
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validateConfiguration(array $configuration): bool
    {
        if (!isset($configuration['color']) || !is_string($configuration['color'])) {
            return false;
        }

        if (isset($configuration['borderSize'])
            && (!is_numeric($configuration['borderSize']) || $configuration['borderSize'] < 0)) {
            return false;
        }

        return true;
    }
}
