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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Image\Modifier;

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\FrameModifier;
use PHPUnit\Framework\TestCase;

/**
 * FrameModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrameModifierTest extends TestCase
{
    use CreatesTestImageTrait;

    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new FrameModifier(['color' => '#ff0000']);
        self::assertInstanceOf(FrameModifier::class, $modifier);
    }

    public function testInstantiationWithOptionalBorderSize(): void
    {
        $modifier = new FrameModifier([
            'color' => '#00ff00',
            'borderSize' => 10,
        ]);
        self::assertInstanceOf(FrameModifier::class, $modifier);
    }

    public function testInstantiationWithDifferentColors(): void
    {
        $modifier = new FrameModifier(['color' => 'blue']);
        self::assertInstanceOf(FrameModifier::class, $modifier);

        $modifier2 = new FrameModifier(['color' => '#336699']);
        self::assertInstanceOf(FrameModifier::class, $modifier2);

        $modifier3 = new FrameModifier(['color' => 'rgba(255, 0, 0, 0.5)']);
        self::assertInstanceOf(FrameModifier::class, $modifier3);
    }

    public function testInstantiationWithCustomBorderSize(): void
    {
        $modifier = new FrameModifier([
            'color' => '#000000',
            'borderSize' => 2,
        ]);
        self::assertInstanceOf(FrameModifier::class, $modifier);
    }

    public function testModifyDrawsBorderWithCustomConfiguration(): void
    {
        $image = $this->createImage();
        $modifier = new FrameModifier(['color' => '#ff0000', 'borderSize' => 3]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
        self::assertSame(64, $image->height());
    }

    public function testModifyUsesDefaultColorAndBorderSize(): void
    {
        $image = $this->createImage();
        $modifier = new FrameModifier([]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
    }
}
