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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageDriverUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ImageDriverUtilityTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]);
    }

    public function testGetImageDriverConfigurationReturnsGdWhenNotConfigured(): void
    {
        self::assertSame(ImageDriverUtility::IMAGE_DRIVER_GD, ImageDriverUtility::getImageDriverConfiguration());
    }

    public function testGetImageDriverConfigurationReturnsConfiguredDriver(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [
            'general' => ['imageDriver' => ImageDriverUtility::IMAGE_DRIVER_IMAGICK],
        ];

        self::assertSame(ImageDriverUtility::IMAGE_DRIVER_IMAGICK, ImageDriverUtility::getImageDriverConfiguration());
    }

    public function testResolveDriverReturnsGdDriverByDefault(): void
    {
        self::assertInstanceOf(GdDriver::class, ImageDriverUtility::resolveDriver());
    }
}
