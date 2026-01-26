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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\OverlayModifier;
use PHPUnit\Framework\TestCase;

/**
 * OverlayModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class OverlayModifierTest extends TestCase
{
    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new OverlayModifier([
            'path' => 'EXT:site/Resources/Public/Images/watermark.png',
            'size' => 0.3,
            'position' => 'top left',
            'padding' => 0.05,
        ]);

        self::assertInstanceOf(OverlayModifier::class, $modifier);
    }

    public function testInstantiationWithBottomRightPosition(): void
    {
        $modifier = new OverlayModifier([
            'path' => 'EXT:extension/Resources/Public/badge.svg',
            'size' => 0.4,
            'position' => 'bottom right',
            'padding' => 0.1,
        ]);

        self::assertInstanceOf(OverlayModifier::class, $modifier);
    }

    public function testInstantiationWithTopCenterPosition(): void
    {
        $modifier = new OverlayModifier([
            'path' => 'EXT:site/Resources/Public/logo.png',
            'size' => 0.6,
            'position' => 'top center',
            'padding' => 0.2,
        ]);

        self::assertInstanceOf(OverlayModifier::class, $modifier);
    }

    public function testInstantiationWithDifferentSizes(): void
    {
        $modifier1 = new OverlayModifier([
            'path' => 'EXT:site/Resources/Public/small.png',
            'size' => 0.1,
            'position' => 'center',
            'padding' => 0.01,
        ]);
        self::assertInstanceOf(OverlayModifier::class, $modifier1);

        $modifier2 = new OverlayModifier([
            'path' => 'EXT:site/Resources/Public/large.png',
            'size' => 0.8,
            'position' => 'center',
            'padding' => 0.05,
        ]);
        self::assertInstanceOf(OverlayModifier::class, $modifier2);
    }

    public function testValidateConfigurationForMissingPath(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['size' => 0.5, 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonStringPath(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 123, 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForMissingSize(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonNumericSize(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 'large', 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForSizeZero(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0, 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForSizeTooLarge(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 1.5, 'position' => 'center', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForMissingPosition(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonStringPosition(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 123, 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForInvalidPosition(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'middle', 'padding' => 0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForMissingPadding(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center']);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonNumericPadding(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 'small']);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForPaddingTooSmall(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => -0.1]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForPaddingTooLarge(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 1.5]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForValidConfiguration(): void
    {
        $modifier = new OverlayModifier(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1]);

        self::assertTrue($result);
    }
}
