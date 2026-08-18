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
use RuntimeException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ColorizeModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorizeModifierTest extends TestCase
{
    use CreatesTestImageTrait;

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

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

    public function testModifyThrowsExceptionWhenNotUsingImagickDriver(): void
    {
        $extConfigMock = $this->createMock(ExtensionConfiguration::class);
        $extConfigMock->method('get')->willReturn(['general' => ['imageDriver' => 'gd']]);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extConfigMock);

        $image = $this->createImage();
        $modifier = new ColorizeModifier(['color' => '#ff0000']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1741785764);
        $modifier->modify($image);
    }

    public function testModifyAppliesColorizeWithImagickDriver(): void
    {
        $extConfigMock = $this->createMock(ExtensionConfiguration::class);
        $extConfigMock->method('get')->willReturn(['general' => ['imageDriver' => 'imagick']]);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $extConfigMock);

        $image = $this->createImagickImage();
        $modifier = new ColorizeModifier(['color' => '#ff0000', 'opacity' => 0.5, 'brightness' => 10, 'contrast' => 5]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
    }

    public function testValidateConfigurationReturnsTrueForFullConfiguration(): void
    {
        $modifier = new ColorizeModifier(['color' => '#fff']);
        self::assertTrue($modifier->validateConfiguration([
            'color' => '#ff0000',
            'opacity' => 0.8,
            'brightness' => 50,
            'contrast' => -10,
        ]));
    }
}
