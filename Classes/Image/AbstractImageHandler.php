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

namespace KonradMichalik\Typo3EnvironmentIndicator\Image;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use KonradMichalik\PhpIcoFileLoader\IcoFileService;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{GeneralHelper, ImageDriverUtility, ImageManagerHelper, SvgRasterizer};
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\{GeneralUtility, PathUtility};

use function is_object;
use function is_string;

/**
 * AbstractImageHandler.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
abstract class AbstractImageHandler
{
    /**
     * @var array<string|int, mixed>
     */
    protected array $imageModifiers = [];

    public function __construct(
        protected readonly IndicatorInterface $indicator,
    ) {
        $this->imageModifiers = $this->getImageModifiers();
    }

    public function process(string $path): string
    {
        if (!$this->shouldProcessImage($path)) {
            return $path;
        }

        if ([] === $this->imageModifiers) {
            return $path;
        }

        $newImageFilename = $this->generateFilename($path).'.png';
        $newImagePath = GeneralHelper::getFolder($this->indicator, false).$newImageFilename;

        // @codeCoverageIgnoreStart
        // Unreachable in practice: $newImagePath is a SHA-256 hash of (among other
        // things) $path itself, so this guards against a hash fixed point that
        // cannot occur.
        if ($path === $newImagePath) {
            return $newImagePath;
        }
        // @codeCoverageIgnoreEnd

        $absoluteNewImagePath = GeneralHelper::getFolder($this->indicator).$newImageFilename;
        if (file_exists($absoluteNewImagePath)) {
            return $newImagePath;
        }

        if (!$this->processAndSaveImage($path, $newImageFilename)) {
            return $path;
        }

        return $newImagePath;
    }

    final protected function generateFilename(string $originalPath): string
    {
        $parts = [
            $originalPath,
            Environment::getContext()->__toString(),
        ];
        foreach ($this->imageModifiers as $key => $configuration) {
            if ($configuration instanceof ModifierInterface) {
                $parts[] = $configuration::class;
                $parts[] = json_encode($configuration->getConfiguration());
            } else {
                $parts[] = (string) $key;
                $parts[] = json_encode($configuration);
            }
        }

        return hash('sha256', implode('_', $parts));
    }

    final protected function convertIcoToPng(string $path, string $filename): string
    {
        $loader = new IcoFileService();
        $icoImage = $loader->fromFile($path);

        foreach ($icoImage as $idx => $image) {
            $basePath = Environment::getPublicPath().'/'.GeneralHelper::getFolder($this->indicator, false).'processed/';
            if (!file_exists($basePath)) {
                GeneralUtility::mkdir_deep($basePath);
            }

            $targetPath = $basePath.$idx.'--'.$filename;

            if (file_exists($targetPath)) {
                $path = $targetPath;
                continue;
            }

            $tmp = $loader->renderImage($image);

            // Write to a temporary file first and move it into place atomically, so
            // concurrent requests never read a half-written image as a cache hit.
            $temporaryPath = $basePath.'.tmp-'.bin2hex(random_bytes(8)).'-'.$idx.'--'.$filename;
            imagepng($tmp, $temporaryPath);

            if (!rename($temporaryPath, $targetPath)) {
                // @codeCoverageIgnoreStart
                // Unreachable in practice: rename() only fails here on OS-level
                // filesystem errors (permissions, disk full, concurrent removal),
                // which cannot be triggered deterministically in a test.
                if (is_file($temporaryPath)) {
                    unlink($temporaryPath);
                }

                continue;
                // @codeCoverageIgnoreEnd
            }

            $path = $targetPath;
        }

        return $path;
    }

    /**
     * @return string|null null when the SVG could not be rasterized or cached, meaning
     *                     $path was left untouched and must not be handed to
     *                     Intervention: GD cannot decode raw SVG at all, and Imagick's
     *                     generic SVG decoding flattens transparency onto an opaque
     *                     background
     */
    final protected function convertSvgToPng(string $path, string $filename): ?string
    {
        $basePath = Environment::getPublicPath().'/'.GeneralHelper::getFolder($this->indicator, false).'processed/';
        if (!file_exists($basePath)) {
            GeneralUtility::mkdir_deep($basePath);
        }

        $svgPath = $path;
        $targetPath = $basePath.'--'.$filename;

        if (file_exists($targetPath)) {
            return $targetPath;
        }

        $rasterImage = SvgRasterizer::rasterize($svgPath);

        if (null === $rasterImage) {
            return null;
        }

        // Write to a temporary file first and move it into place atomically, so
        // concurrent requests never read a half-written image as a cache hit.
        $temporaryPath = $basePath.'.tmp-'.bin2hex(random_bytes(8)).'--'.$filename;
        imagepng($rasterImage, $temporaryPath);

        if (!rename($temporaryPath, $targetPath)) {
            // @codeCoverageIgnoreStart
            // Unreachable in practice: rename() only fails here on OS-level
            // filesystem errors (permissions, disk full, concurrent removal),
            // which cannot be triggered deterministically in a test.
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            return null;
            // @codeCoverageIgnoreEnd
        }

        return $targetPath;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getImageModifiers(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[$this->indicator::class] ?? [];
    }

    private function shouldProcessImage(string $path): bool
    {
        // Cheap in-memory check first; only hit the filesystem when an
        // indicator is actually active (never the case on production).
        if (!GeneralHelper::isCurrentIndicator($this->indicator::class)) {
            return false;
        }

        return file_exists(GeneralUtility::getFileAbsFileName($path));
    }

    private function processAndSaveImage(string $path, string $newImageFilename): bool
    {
        $manager = new ImageManager(ImageDriverUtility::resolveDriver());
        $absolutePath = PathUtility::isAbsolutePath($path) ? $path : GeneralUtility::getFileAbsFileName($path);

        $format = pathinfo($absolutePath, \PATHINFO_EXTENSION);
        if (!GeneralHelper::supportFormat($manager, $format)) {
            return false;
        }

        $resolvedPath = $this->preProcessImage($absolutePath, $newImageFilename, $format);
        if (null === $resolvedPath) {
            return false;
        }

        $image = ImageManagerHelper::readImage($manager, $resolvedPath);
        $this->applyImageModifiers($image);

        $folder = GeneralHelper::getFolder($this->indicator);
        $targetPath = $folder.$newImageFilename;

        // Write to a temporary file first and move it into place atomically, so
        // concurrent requests never read a half-written image as a cache hit.
        $temporaryPath = $folder.'.tmp-'.bin2hex(random_bytes(8)).'-'.$newImageFilename;
        $image->save($temporaryPath);

        if (!rename($temporaryPath, $targetPath)) {
            // @codeCoverageIgnoreStart
            // Unreachable in practice: rename() only fails here on OS-level
            // filesystem errors (permissions, disk full, concurrent removal),
            // which cannot be triggered deterministically in a test.
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            return false;
            // @codeCoverageIgnoreEnd
        }

        return true;
    }

    private function applyImageModifiers(ImageInterface &$image): void
    {
        foreach ($this->imageModifiers as $key => $modifier) {
            if (is_string($key) && str_starts_with($key, '_')) {
                continue;
            }

            if (!is_object($modifier) || !method_exists($modifier, 'modify')) {
                continue;
            }

            $modifier->modify($image);
        }
    }

    private function preProcessImage(string $absolutePath, string $newImageFilename, string $format): ?string
    {
        /*
        * GD driver does not support .ico files, so we need to convert them to .png before processing them
        */
        if (ImageDriverUtility::IMAGE_DRIVER_GD === ImageDriverUtility::getImageDriverConfiguration() && 'ico' === $format) {
            $absolutePath = $this->convertIcoToPng($absolutePath, $newImageFilename);
        }

        if ('svg' === $format) {
            return $this->convertSvgToPng($absolutePath, $newImageFilename);
        }

        return $absolutePath;
    }
}
