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

use Imagick;
use ImagickPixel;
use Intervention\Image\Interfaces\ImageInterface;
use KonradMichalik\Color\Color;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageDriverUtility;
use RuntimeException;

use function array_key_exists;
use function assert;
use function is_string;
use function sprintf;

/**
 * ColorizeModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorizeModifier extends AbstractModifier implements ModifierInterface
{
    public function modify(ImageInterface &$image): void
    {
        if ('imagick' !== ImageDriverUtility::getImageDriverConfiguration()) {
            throw new RuntimeException('This modifier requires the Imagick driver', 1741785764);
        }

        $targetColor = (Color::tryFromString($this->configuration['color']) ?? Color::fromRgb(0, 0, 0))->toRgb()->toCssString();
        $opacityPercentage = (($this->configuration['opacity'] ?? 1) * 100).'%';

        $imagick = $image->core()->native();
        assert($imagick instanceof Imagick);
        $imagick->modulateImage(100, 0, 100);
        $color = new ImagickPixel($targetColor);
        $opacity = new ImagickPixel(sprintf('rgb(%s, %s, %s)', $opacityPercentage, $opacityPercentage, $opacityPercentage));

        $imagick->colorizeImage($color, $opacity);

        if (array_key_exists('brightness', $this->configuration)) {
            $image->brightness((int) $this->configuration['brightness']);
        }

        if (array_key_exists('contrast', $this->configuration)) {
            $image->contrast((int) $this->configuration['contrast']);
        }
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validateConfiguration(array $configuration): bool
    {
        if (!isset($configuration['color']) || !is_string($configuration['color'])) {
            return false;
        }

        if (isset($configuration['opacity'])
            && (!is_numeric($configuration['opacity']) || $configuration['opacity'] < 0 || $configuration['opacity'] > 1)) {
            return false;
        }

        if (isset($configuration['brightness']) && !is_numeric($configuration['brightness'])) {
            return false;
        }

        if (isset($configuration['contrast']) && !is_numeric($configuration['contrast'])) {
            return false;
        }

        return true;
    }
}
