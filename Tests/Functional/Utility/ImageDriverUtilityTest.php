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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Utility;

use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageDriverUtility;
use RuntimeException;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageDriverUtilityTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageDriverUtilityTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => []]]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testGetImageDriverConfigurationReturnsGdWhenNotConfigured(): void
    {
        self::assertSame(ImageDriverUtility::IMAGE_DRIVER_GD, ImageDriverUtility::getImageDriverConfiguration());
    }

    public function testGetImageDriverConfigurationReturnsConfiguredDriver(): void
    {
        $this->setTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => [
            'general' => ['imageDriver' => ImageDriverUtility::IMAGE_DRIVER_IMAGICK],
        ]]]);

        self::assertSame(ImageDriverUtility::IMAGE_DRIVER_IMAGICK, ImageDriverUtility::getImageDriverConfiguration());
    }

    public function testResolveDriverReturnsGdDriverByDefault(): void
    {
        self::assertInstanceOf(GdDriver::class, ImageDriverUtility::resolveDriver());
    }

    public function testResolveDriverReturnsImagickDriverWhenConfigured(): void
    {
        $this->setTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => [
            'general' => ['imageDriver' => ImageDriverUtility::IMAGE_DRIVER_IMAGICK],
        ]]]);

        self::assertInstanceOf(ImagickDriver::class, ImageDriverUtility::resolveDriver());
    }

    public function testResolveDriverThrowsExceptionWhenVipsDriverUnavailable(): void
    {
        $this->setTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => [
            'general' => ['imageDriver' => ImageDriverUtility::IMAGE_DRIVER_VIPS],
        ]]]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(1741785476);

        ImageDriverUtility::resolveDriver();
    }
}
