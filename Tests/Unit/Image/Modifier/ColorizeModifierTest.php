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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ColorizeModifier;
use PHPUnit\Framework\TestCase;

/**
 * ColorizeModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorizeModifierTest extends TestCase
{
    public function testInstantiationWithRequiredValues(): void
    {
        $modifier = new ColorizeModifier(['color' => '#ff0000']);
        self::assertInstanceOf(ColorizeModifier::class, $modifier);
    }

    public function testInstantiationWithOptionalValues(): void
    {
        $modifier = new ColorizeModifier([
            'color' => '#ff0000',
            'opacity' => 0.5,
            'brightness' => 50,
            'contrast' => 25,
        ]);
        self::assertInstanceOf(ColorizeModifier::class, $modifier);
    }

    public function testRequiresImagickDriver(): void
    {
        $modifier = new ColorizeModifier(['color' => '#ff0000']);
        self::assertInstanceOf(ColorizeModifier::class, $modifier);
    }
}
