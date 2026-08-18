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
}
