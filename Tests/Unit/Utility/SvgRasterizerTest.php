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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Utility;

use GdImage;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\SvgRasterizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function imagesx;
use function imagesy;

/**
 * SvgRasterizerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class SvgRasterizerTest extends TestCase
{
    public function testRasterizeReturnsNullForXmlWithoutSvgRoot(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/not-an-svg.xml');

        self::assertNull($result);
    }

    public function testRasterizeReturnsNullForMalformedXml(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/malformed.svg');

        self::assertNull($result);
    }

    public function testRasterizeUsesExplicitWidthAndHeight(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(16, imagesx($result));
        self::assertSame(16, imagesy($result));
    }

    public function testRasterizeFallsBackToViewBoxDimensions(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test-no-dims.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(32, imagesx($result));
        self::assertSame(32, imagesy($result));
    }

    public function testRasterizeFallsBackToDefaultSizeWhenNeitherDimensionsNorViewBoxArePresent(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test-no-dims-no-viewbox.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(64, imagesx($result));
        self::assertSame(64, imagesy($result));
    }

    public function testRasterizeClampsOversizedSvgKeepingTheAspectRatio(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test-oversized.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(1024, imagesx($result));
        self::assertSame(512, imagesy($result));
    }

    public function testRasterizeClampsTallSvgKeepingTheAspectRatio(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test-oversized-tall.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(10, imagesx($result));
        self::assertSame(1024, imagesy($result));
    }

    #[DataProvider('externalImageReferenceProvider')]
    public function testRasterizeReturnsNullForImageWithExternalReference(string $fixture): void
    {
        self::assertNull(SvgRasterizer::rasterize(__DIR__.'/Fixtures/'.$fixture));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function externalImageReferenceProvider(): iterable
    {
        yield 'local path via xlink:href' => ['test-image-local-path.svg'];
        yield 'remote url via xlink:href' => ['test-image-remote-url.svg'];
        yield 'nested image via plain href' => ['test-image-nested-plain-href.svg'];
        yield 'svg embedded as data uri' => ['test-image-nested-svg-data-uri.svg'];
        yield 'svg in data uri with a stray base64 character' => ['test-image-nested-svg-invalid-base64.svg'];
        yield 'uppercase data scheme' => ['test-image-uppercase-data-scheme.svg'];
    }

    public function testRasterizeKeepsImageWithEmbeddedDataUri(): void
    {
        $result = SvgRasterizer::rasterize(__DIR__.'/Fixtures/test-image-data-uri.svg');

        self::assertInstanceOf(GdImage::class, $result);
        self::assertSame(16, imagesx($result));
    }
}
