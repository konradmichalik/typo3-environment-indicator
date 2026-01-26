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
use PHPUnit\Framework\TestCase;

/**
 * TriangleModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TriangleModifierTest extends TestCase
{
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

    public function testValidateConfigurationForMissingColor(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration([]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonStringColor(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => 123]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonNumericSize(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'size' => 'large']);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForSizeTooSmall(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'size' => -0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForSizeTooLarge(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'size' => 1.5]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonStringPosition(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'position' => 123]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForInvalidPosition(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'position' => 'middle']);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForValidConfiguration(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff']);

        self::assertTrue($result);
    }

    public function testValidateConfigurationForValidConfigurationWithOptionals(): void
    {
        $modifier = new TriangleModifier(['color' => '#fff']);
        $result = $modifier->validateConfiguration(['color' => '#fff', 'size' => 0.5, 'position' => 'top left']);

        self::assertTrue($result);
    }
}
