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
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{GeneralHelper, ImageDriverUtility, ImageManagerHelper};
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

        if ($path === $newImagePath) {
            return $newImagePath;
        }

        $absoluteNewImagePath = GeneralHelper::getFolder($this->indicator).$newImageFilename;
        if (file_exists($absoluteNewImagePath)) {
            return $newImagePath;
        }

        if (!$this->processAndSaveImage($path, $newImageFilename)) {
            return $path;
        }

        return $newImagePath;
    }

    protected function generateFilename(string $originalPath): string
    {
        $parts = [
            $originalPath,
            Environment::getContext()->__toString(),
        ];
        foreach ($this->imageModifiers as $modifier => $configuration) {
            $parts[] = $modifier;
            $parts[] = json_encode($configuration);
        }

        return hash('sha256', implode('_', $parts));
    }

    protected function convertIcoToPng(string &$path, string $filename): void
    {
        $loader = new IcoFileService();
        $icoImage = $loader->fromFile($path);

        foreach ($icoImage as $idx => $image) {
            $tmp = $loader->renderImage($image);

            $basePath = Environment::getPublicPath().'/'.GeneralHelper::getFolder($this->indicator, false).'processed/';
            if (!file_exists($basePath)) {
                GeneralUtility::mkdir_deep($basePath);
            }

            $path = $basePath.$idx.'--'.$filename;

            if (file_exists($path)) {
                continue;
            }
            imagepng($tmp, $path);
        }
    }

    /*
    * @see https://github.com/meyfa/php-svg?tab=readme-ov-file#rasterizing
    * Notes from the author:
    * This feature in particular is very much work-in-progress. Many things will look wrong and rendering large images may be very slow.
    */
    protected function convertSvgToPng(string &$path, string $filename): void
    {
        $loader = new \SVG\SVG();
        $svgImage = $loader::fromFile($path);

        if (null === $svgImage) {
            return;
        }

        $document = $svgImage->getDocument();
        $width = (int) $document->getWidth();
        $height = (int) $document->getHeight();

        // Try to extract dimensions from viewBox if width/height are not set
        if ($width <= 0 || $height <= 0) {
            $viewBox = $document->getViewBox();
            if (null !== $viewBox) {
                $width = (int) $viewBox[2];
                $height = (int) $viewBox[3];
            }
        }

        // Fallback to default size if still invalid
        if ($width <= 0 || $height <= 0) {
            $width = 64;
            $height = 64;
        }

        $basePath = Environment::getPublicPath().'/'.GeneralHelper::getFolder($this->indicator, false).'processed/';
        if (!file_exists($basePath)) {
            GeneralUtility::mkdir_deep($basePath);
        }

        $path = $basePath.'--'.$filename;

        if (file_exists($path)) {
            return;
        }

        $rasterImage = $svgImage->toRasterImage($width, $height);
        imagepng($rasterImage, $path); // @phpstan-ignore-line
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
        $absolutePath = GeneralUtility::getFileAbsFileName($path);

        if (!file_exists($absolutePath)) {
            return false;
        }

        if (!GeneralHelper::isCurrentIndicator($this->indicator::class)) {
            return false;
        }

        return true;
    }

    private function processAndSaveImage(string $path, string $newImageFilename): bool
    {
        $manager = new ImageManager(ImageDriverUtility::resolveDriver());
        $absolutePath = PathUtility::isAbsolutePath($path) ? $path : GeneralUtility::getFileAbsFileName($path);

        $format = pathinfo($absolutePath, \PATHINFO_EXTENSION);
        if (!GeneralHelper::supportFormat($manager, $format)) {
            return false;
        }

        $this->preProcessImage($absolutePath, $newImageFilename, $format);

        $image = ImageManagerHelper::readImage($manager, $absolutePath);
        $this->applyImageModifiers($image);
        $image->save(GeneralHelper::getFolder($this->indicator).$newImageFilename);

        return true;
    }

    private function applyImageModifiers(ImageInterface $image): void
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

    private function preProcessImage(string &$absolutePath, string &$newImageFilename, string $format): void
    {
        /*
        * GD driver does not support .ico files, so we need to convert them to .png before processing them
        */
        if (ImageDriverUtility::IMAGE_DRIVER_GD === ImageDriverUtility::getImageDriverConfiguration() && 'ico' === $format) {
            $this->convertIcoToPng($absolutePath, $newImageFilename);
        }

        if ('svg' === $format) {
            $this->convertSvgToPng($absolutePath, $newImageFilename);
        }
    }
}
