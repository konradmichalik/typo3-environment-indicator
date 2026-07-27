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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\CircleModifier;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * CircleModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class CircleModifierTest extends TestCase
{
    use CreatesTestImageTrait;

    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new CircleModifier([
            'color' => '#ff0000',
            'size' => 0.4,
            'padding' => 0.1,
            'position' => 'bottom right',
        ]);

        self::assertInstanceOf(CircleModifier::class, $modifier);
    }

    public function testInstantiationWithCustomValues(): void
    {
        $modifier = new CircleModifier([
            'color' => '#00ff00',
            'size' => 0.6,
            'padding' => 0.2,
            'position' => 'top left',
        ]);

        self::assertInstanceOf(CircleModifier::class, $modifier);
    }

    public function testValidateConfigurationForInvalidPosition(): void
    {
        $modifier = new CircleModifier(['color' => '#fff', 'size' => 0.5, 'padding' => 0.1, 'position' => 'top left']);
        $result = $modifier->validateConfigurationWithErrors(['color' => '#fff', 'size' => 0.5, 'padding' => 0.1, 'position' => 'middle']);

        self::assertFalse($result['valid']);
        self::assertStringContainsString('Configuration key "position" must be one of:', $result['errors'][0]);
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
    public function testModifyDrawsCircleForEveryPosition(string $position): void
    {
        $image = $this->createImage();
        $modifier = new CircleModifier(['color' => '#ff0000', 'size' => 0.4, 'padding' => 0.1, 'position' => $position]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
        self::assertSame(64, $image->height());
    }
}
