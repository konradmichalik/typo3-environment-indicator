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
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{CircleModifier, ColorizeModifier, FrameModifier, ModifierInterface, OverlayModifier, ReplaceModifier, TextModifier, TriangleModifier};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * ModifierConstructorValidationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ModifierConstructorValidationTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<ModifierInterface>, array<string, mixed>, array<string, mixed>}>
     */
    public static function modifierProvider(): iterable
    {
        yield 'colorize' => [ColorizeModifier::class, ['color' => '#ff0000'], ['color' => '#ff0000', 'opacity' => 2]];
        yield 'frame' => [FrameModifier::class, ['color' => '#ff0000'], ['color' => '#ff0000', 'borderSize' => -1]];
        yield 'overlay' => [OverlayModifier::class, ['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1], ['path' => 'EXT:test.png', 'size' => 2, 'position' => 'center', 'padding' => 0.1]];
        yield 'replace' => [ReplaceModifier::class, ['path' => 'EXT:test.png'], ['path' => 42]];
        yield 'triangle' => [TriangleModifier::class, ['color' => '#ff0000'], ['color' => '#ff0000', 'position' => 'middle']];
        yield 'text' => [TextModifier::class, ['text' => 'DEV', 'color' => '#ff0000'], ['text' => '', 'color' => '#ff0000']];
        yield 'circle' => [CircleModifier::class, ['color' => '#ff0000', 'size' => 0.5, 'padding' => 0.1, 'position' => 'top left'], ['color' => 42, 'size' => 0.5, 'padding' => 0.1, 'position' => 'top left']];
    }

    /**
     * @param class-string<ModifierInterface> $modifier
     */
    #[DataProvider('modifierProvider')]
    public function testConstructorRejectsEmptyConfiguration(string $modifier): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1740401564);
        $this->expectExceptionMessage($modifier);

        new $modifier([]);
    }

    /**
     * @param class-string<ModifierInterface> $modifier
     * @param array<string, mixed>            $valid
     * @param array<string, mixed>            $invalid
     */
    #[DataProvider('modifierProvider')]
    public function testConstructorRejectsInvalidConfigurationWithDescriptiveMessage(string $modifier, array $valid, array $invalid): void
    {
        $errors = (new $modifier($valid))->validateConfigurationWithErrors($invalid)['errors'];
        self::assertNotSame([], $errors);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($errors[0]);

        new $modifier($invalid);
    }

    /**
     * @param class-string<ModifierInterface> $modifier
     * @param array<string, mixed>            $valid
     * @param array<string, mixed>            $invalid
     */
    #[DataProvider('modifierProvider')]
    public function testConstructorAndValidateConfigurationAgree(string $modifier, array $valid, array $invalid): void
    {
        $validator = new $modifier($valid);

        foreach ([[], $valid, $invalid] as $configuration) {
            try {
                new $modifier($configuration);
                $constructorAccepts = true;
            } catch (InvalidArgumentException) {
                $constructorAccepts = false;
            }

            self::assertSame($validator->validateConfiguration($configuration), $constructorAccepts);
        }
    }
}
