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
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ImageDriverUtility, ImageManagerHelper, SvgRasterizer};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function is_string;

/**
 * ReplaceModifier.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ReplaceModifier extends AbstractModifier implements ModifierInterface
{
    public function modify(ImageInterface &$image): void
    {
        $manager = new ImageManager(
            ImageDriverUtility::resolveDriver(),
        );
        $path = GeneralUtility::getFileAbsFileName($this->configuration['path']);

        if ('svg' === pathinfo($path, \PATHINFO_EXTENSION)) {
            $replacement = $this->readSvg($manager, $path);

            if (null !== $replacement) {
                $image = $replacement;
            }

            return;
        }

        $image = ImageManagerHelper::readImage($manager, $path);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    public function validateConfiguration(array $configuration): bool
    {
        if (!isset($configuration['path']) || !is_string($configuration['path'])) {
            return false;
        }

        return true;
    }

    /**
     * Rasterizes SVG sources through meyfa/php-svg before handing them to
     * Intervention: GD cannot decode SVG at all, and Imagick's generic SVG
     * decoding flattens transparency onto an opaque background.
     *
     * Returns null when the SVG cannot be rasterized or cached, so the caller
     * can keep the original image instead of handing the raw SVG to
     * Intervention, which would reintroduce exactly those two problems.
     */
    private function readSvg(ImageManager $manager, string $path): ?ImageInterface
    {
        $rasterImage = SvgRasterizer::rasterize($path);

        if (null === $rasterImage) {
            return null;
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'typo3_environment_indicator_svg_');
        if (false === $temporaryPath) {
            // @codeCoverageIgnoreStart
            // Unreachable in practice: tempnam() only fails here on OS-level
            // filesystem errors (permissions, disk full), which cannot be
            // triggered deterministically in a test.
            return null;
            // @codeCoverageIgnoreEnd
        }

        try {
            imagepng($rasterImage, $temporaryPath);

            return ImageManagerHelper::readImage($manager, $temporaryPath);
        } finally {
            unlink($temporaryPath);
        }
    }
}
