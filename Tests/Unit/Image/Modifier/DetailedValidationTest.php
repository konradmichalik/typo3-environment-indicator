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

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{CircleModifier, TextModifier};
use PHPUnit\Framework\TestCase;

/**
 * DetailedValidationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class DetailedValidationTest extends TestCase
{
    public function testTextModifierDetailedValidationErrors(): void
    {
        $modifier = new TextModifier(['text' => 'test', 'color' => 'red']);

        $result = $modifier->validateConfigurationWithErrors([
            'color' => 'red',
            // missing 'text' key
        ]);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required configuration key: text', $result['errors']);
    }

    public function testTextModifierEmptyTextError(): void
    {
        $modifier = new TextModifier(['text' => 'test', 'color' => 'red']);

        $result = $modifier->validateConfigurationWithErrors([
            'text' => '   ', // empty text
            'color' => 'red',
        ]);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "text" cannot be empty', $result['errors']);
    }

    public function testTextModifierInvalidPositionError(): void
    {
        $modifier = new TextModifier(['text' => 'test', 'color' => 'red']);

        $result = $modifier->validateConfigurationWithErrors([
            'text' => 'test',
            'color' => 'red',
            'position' => 'invalid',
        ]);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "position" must be one of: top, bottom', $result['errors']);
    }

    public function testCircleModifierDetailedValidationErrors(): void
    {
        $modifier = new CircleModifier([
            'color' => 'red',
            'size' => 0.5,
            'padding' => 0.1,
            'position' => 'bottom right',
        ]);

        $result = $modifier->validateConfigurationWithErrors([
            'color' => 'red',
            'size' => 1.5, // invalid size
            'padding' => 0.1,
            'position' => 'bottom right',
        ]);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "size" must be between 0 and 1', $result['errors']);
    }

    public function testCircleModifierMissingRequiredKeys(): void
    {
        $modifier = new CircleModifier([
            'color' => 'red',
            'size' => 0.5,
            'padding' => 0.1,
            'position' => 'bottom right',
        ]);

        $result = $modifier->validateConfigurationWithErrors([
            'color' => 'red',
            // missing size, padding, position
        ]);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required configuration key: size', $result['errors']);
        self::assertContains('Missing required configuration key: padding', $result['errors']);
        self::assertContains('Missing required configuration key: position', $result['errors']);
    }

    public function testConstructorWithDetailedErrorMessage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid configuration for KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier: Missing required configuration key: text, Missing required configuration key: color');

        new TextModifier([]);
    }
}
