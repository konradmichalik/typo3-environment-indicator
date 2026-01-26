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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier;
use PHPUnit\Framework\TestCase;

/**
 * TextModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TextModifierTest extends TestCase
{
    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new TextModifier([
            'text' => 'Development',
            'color' => '#ffffff',
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
    }

    public function testInstantiationWithOptionalValues(): void
    {
        $modifier = new TextModifier([
            'text' => 'Staging',
            'color' => '#ffffff',
            'font' => 'EXT:site/Resources/Private/Fonts/arial.ttf',
            'position' => 'top',
            'stroke' => [
                'color' => '#000000',
                'width' => 2,
            ],
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
    }

    public function testInstantiationWithStrokeConfiguration(): void
    {
        $modifier = new TextModifier([
            'text' => 'Production',
            'color' => '#ff0000',
            'stroke' => [
                'color' => '#000000',
                'width' => 1,
            ],
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
    }

    public function testInstantiationWithPositionTop(): void
    {
        $modifier = new TextModifier([
            'text' => 'Local',
            'color' => '#00ff00',
            'position' => 'top',
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
    }

    public function testInstantiationWithPositionBottom(): void
    {
        $modifier = new TextModifier([
            'text' => 'Testing',
            'color' => '#0000ff',
            'position' => 'bottom',
        ]);

        self::assertInstanceOf(TextModifier::class, $modifier);
    }

    public function testValidateConfigurationWithErrorsForMissingText(): void
    {
        $modifier = new TextModifier(['text' => 'Valid', 'color' => '#ffffff']);
        $result = $modifier->validateConfigurationWithErrors(['color' => '#ffffff']);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required configuration key: text', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonStringText(): void
    {
        $modifier = new TextModifier(['text' => 'Valid', 'color' => '#ffffff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 123, 'color' => '#ffffff']);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "text" must be a string', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForEmptyText(): void
    {
        $modifier = new TextModifier(['text' => 'Valid', 'color' => '#ffffff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => '   ', 'color' => '#ffffff']);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "text" cannot be empty', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForMissingColor(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#ffffff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test']);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required configuration key: color', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonStringColor(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#ffffff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => 123]);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "color" must be a string', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForInvalidPosition(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'position' => 'middle']);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "position" must be one of: top, bottom', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonStringFont(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'font' => 123]);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "font" must be a string', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonArrayStroke(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'stroke' => 'invalid']);

        self::assertFalse($result['valid']);
        self::assertContains('Configuration key "stroke" must be an array', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForMissingStrokeColor(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'stroke' => ['width' => 2]]);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required stroke configuration key: color', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonStringStrokeColor(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'stroke' => ['color' => 123, 'width' => 2]]);

        self::assertFalse($result['valid']);
        self::assertContains('Stroke configuration key "color" must be a string', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForMissingStrokeWidth(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'stroke' => ['color' => '#000']]);

        self::assertFalse($result['valid']);
        self::assertContains('Missing required stroke configuration key: width', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForNonNumericStrokeWidth(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff', 'stroke' => ['color' => '#000', 'width' => 'two']]);

        self::assertFalse($result['valid']);
        self::assertContains('Stroke configuration key "width" must be numeric', $result['errors']);
    }

    public function testValidateConfigurationWithErrorsForValidConfiguration(): void
    {
        $modifier = new TextModifier(['text' => 'Test', 'color' => '#fff']);
        $result = $modifier->validateConfigurationWithErrors(['text' => 'Test', 'color' => '#fff']);

        self::assertTrue($result['valid']);
        self::assertEmpty($result['errors']);
    }
}
