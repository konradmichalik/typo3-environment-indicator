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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Image\Factory;

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Factory\{ModifierFactory, ModifierFactoryInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{CircleModifier, ColorizeModifier, FrameModifier, ModifierInterface, TextModifier, TriangleModifier};
use PHPUnit\Framework\TestCase;

/**
 * ModifierFactoryTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ModifierFactoryTest extends TestCase
{
    private ModifierFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ModifierFactory();
    }

    public function testImplementsInterface(): void
    {
        self::assertInstanceOf(ModifierFactoryInterface::class, $this->factory);
    }

    public function testCreateTextModifier(): void
    {
        $modifier = $this->factory->createModifier('text', [
            'text' => 'DEV',
            'color' => '#ff0000',
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
        self::assertInstanceOf(ModifierInterface::class, $modifier);
    }

    public function testCreateFrameModifier(): void
    {
        $modifier = $this->factory->createModifier('frame', [
            'color' => '#00ff00',
        ]);

        self::assertInstanceOf(FrameModifier::class, $modifier);
    }

    public function testCreateTriangleModifier(): void
    {
        $modifier = $this->factory->createModifier('triangle', [
            'color' => '#0000ff',
        ]);

        self::assertInstanceOf(TriangleModifier::class, $modifier);
    }

    public function testCreateCircleModifier(): void
    {
        $modifier = $this->factory->createModifier('circle', [
            'color' => '#ff00ff',
            'size' => 0.5,
            'padding' => 0.1,
            'position' => 'bottom right',
        ]);

        self::assertInstanceOf(CircleModifier::class, $modifier);
    }

    public function testCreateColorizeModifier(): void
    {
        $modifier = $this->factory->createModifier('colorize', [
            'color' => '#ffff00',
        ]);

        self::assertInstanceOf(ColorizeModifier::class, $modifier);
    }

    public function testCreateModifierWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1726357771);
        $this->expectExceptionMessage('Unsupported modifier type: invalid');

        $this->factory->createModifier('invalid', []);
    }

    public function testCreateModifierWithMissingRequiredConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1726357772);
        $this->expectExceptionMessage('Invalid configuration for modifier type: text');

        $this->factory->createModifier('text', ['color' => '#ff0000']); // missing 'text' key
    }

    public function testGetSupportedModifierTypes(): void
    {
        $types = $this->factory->getSupportedModifierTypes();

        self::assertContains('text', $types);
        self::assertContains('frame', $types);
        self::assertContains('triangle', $types);
        self::assertContains('circle', $types);
        self::assertContains('colorize', $types);
        self::assertContains('overlay', $types);
        self::assertContains('replace', $types);
        self::assertCount(7, $types);
    }

    public function testValidateConfigurationWithValidConfiguration(): void
    {
        $result = $this->factory->validateConfiguration('text', [
            'text' => 'DEV',
            'color' => '#ff0000',
        ]);

        self::assertTrue($result);
    }

    public function testValidateConfigurationWithInvalidConfiguration(): void
    {
        $result = $this->factory->validateConfiguration('text', [
            'color' => '#ff0000', // missing 'text' key
        ]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationWithInvalidType(): void
    {
        $result = $this->factory->validateConfiguration('invalid', []);
        self::assertFalse($result);
    }
}
