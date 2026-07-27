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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TriangleModifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * TriangleModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TriangleModifierTest extends TestCase
{
    use CreatesTestImageTrait;

    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new TriangleModifier([
            'color' => '#ff0000',
        ]);

        self::assertInstanceOf(TriangleModifier::class, $modifier);
    }

    public function testInstantiationWithCustomValues(): void
    {
        $modifier = new TriangleModifier([
            'color' => '#00ff00',
        ]);

        self::assertInstanceOf(TriangleModifier::class, $modifier);
    }

    public function testValidateConfigurationForInvalidPosition(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'position' => 'middle']);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForValidConfigurationWithOptionals(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'size' => 0.5, 'position' => 'top left']);

        self::assertTrue($result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function positionDataProvider(): array
    {
        return [
            'top left' => ['top left'],
            'top right' => ['top right'],
            'bottom left' => ['bottom left'],
            'bottom right' => ['bottom right'],
        ];
    }

    #[DataProvider('positionDataProvider')]
    public function testModifyDrawsTriangleForEveryPosition(string $position): void
    {
        $image = $this->createImage();
        $modifier = new TriangleModifier(['color' => '#ff0000', 'size' => 0.5, 'position' => $position]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
        self::assertSame(64, $image->height());
    }

    public function testModifyUsesDefaultsWhenOptionalsAreMissing(): void
    {
        $image = $this->createImage();
        $modifier = new TriangleModifier(['color' => '#00ff00']);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
    }
}
