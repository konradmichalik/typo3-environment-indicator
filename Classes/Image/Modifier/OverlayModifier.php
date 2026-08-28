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

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ImageDriverUtility, ImageManagerHelper};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function in_array;
use function is_string;

/**
 * OverlayModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class OverlayModifier extends AbstractModifier implements ModifierInterface
{
    public function modify(ImageInterface $image): void
    {
        $manager = new ImageManager(
            ImageDriverUtility::resolveDriver(),
        );
        $overlay = ImageManagerHelper::readImage($manager, GeneralUtility::getFileAbsFileName($this->configuration['path']));

        $newWidth = (int) ($image->width() * $this->configuration['size']);
        $newHeight = (int) ($overlay->height() * ($newWidth / $overlay->width()));
        $overlay = $overlay->resize($newWidth, $newHeight);

        $paddingX = (int) ($image->width() * $this->configuration['padding']);
        $paddingY = (int) ($image->height() * $this->configuration['padding']);

        $position = str_replace(' ', '-', strtolower((string) $this->configuration['position']));

        ImageManagerHelper::placeImage($image, $overlay, $position, $paddingX, $paddingY);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validateConfiguration(array $configuration): bool
    {
        if (!isset($configuration['path']) || !is_string($configuration['path'])) {
            return false;
        }

        if (!isset($configuration['size']) || !is_numeric($configuration['size'])
            || $configuration['size'] <= 0 || $configuration['size'] > 1) {
            return false;
        }

        if (!isset($configuration['position']) || !is_string($configuration['position'])) {
            return false;
        }

        $validPositions = ['top left', 'top center', 'top right', 'center left', 'center', 'center right', 'bottom left', 'bottom center', 'bottom right'];
        if (!in_array($configuration['position'], $validPositions, true)) {
            return false;
        }

        if (!isset($configuration['padding']) || !is_numeric($configuration['padding'])
            || $configuration['padding'] < 0 || $configuration['padding'] > 1) {
            return false;
        }

        return true;
    }
}
