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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ReplaceModifier;
use PHPUnit\Framework\TestCase;

/**
 * ReplaceModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ReplaceModifierTest extends TestCase
{
    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:extension/Resources/Public/replacement.svg']);
        self::assertInstanceOf(ReplaceModifier::class, $modifier);
    }

    public function testInstantiationWithDifferentPaths(): void
    {
        $modifier1 = new ReplaceModifier(['path' => 'EXT:site/Resources/Public/Images/dev.png']);
        self::assertInstanceOf(ReplaceModifier::class, $modifier1);

        $modifier2 = new ReplaceModifier(['path' => 'EXT:extension/Resources/Public/staging.jpg']);
        self::assertInstanceOf(ReplaceModifier::class, $modifier2);

        $modifier3 = new ReplaceModifier(['path' => 'EXT:custom/Resources/Private/production.gif']);
        self::assertInstanceOf(ReplaceModifier::class, $modifier3);
    }

    public function testInstantiationWithExtensionPath(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:typo3_environment_indicator/Resources/Public/favicon.ico']);
        self::assertInstanceOf(ReplaceModifier::class, $modifier);
    }

    public function testValidateConfigurationForMissingPath(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:test.png']);
        $result = $modifier->validateConfiguration([]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForNonStringPath(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:test.png']);
        $result = $modifier->validateConfiguration(['path' => 123]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForArrayPath(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:test.png']);
        $result = $modifier->validateConfiguration(['path' => ['EXT:test.png']]);

        self::assertFalse($result);
    }

    public function testValidateConfigurationForValidConfiguration(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:test.png']);
        $result = $modifier->validateConfiguration(['path' => 'EXT:test.png']);

        self::assertTrue($result);
    }

    public function testValidateConfigurationForValidPathVariations(): void
    {
        $modifier = new ReplaceModifier(['path' => 'EXT:test.png']);

        self::assertTrue($modifier->validateConfiguration(['path' => 'EXT:site/Resources/Public/image.png']));
        self::assertTrue($modifier->validateConfiguration(['path' => 'EXT:extension/favicon.ico']));
        self::assertTrue($modifier->validateConfiguration(['path' => '/absolute/path/to/file.jpg']));
    }
}
