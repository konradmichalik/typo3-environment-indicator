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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * GeneralHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class GeneralHelperTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults']);
    }

    public function testGetFolderReturnsAbsolutePublicPath(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][Favicon::class] = [
            '_path' => 'typo3temp/assets/test-indicator/',
        ];

        $indicator = new Favicon([]);
        $result = GeneralHelper::getFolder($indicator);

        self::assertStringStartsWith(Environment::getPublicPath(), $result);
        self::assertStringContainsString('typo3temp/assets/test-indicator/', $result);
    }

    public function testGetFolderReturnsRelativePathWhenPublicPathFalse(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][Favicon::class] = [
            '_path' => 'typo3temp/assets/test-indicator/',
        ];

        $indicator = new Favicon([]);
        $result = GeneralHelper::getFolder($indicator, false);

        self::assertSame('typo3temp/assets/test-indicator/', $result);
    }

    public function testGetFolderCreatesDirectoryIfNotExists(): void
    {
        $testPath = 'typo3temp/assets/test-indicator-'.uniqid().'/';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][Favicon::class] = [
            '_path' => $testPath,
        ];

        $indicator = new Favicon([]);
        GeneralHelper::getFolder($indicator);

        self::assertDirectoryExists(Environment::getPublicPath().'/'.$testPath);
    }

    public function testIsMinimumTypo3VersionReturnsTrueForCurrentMajorVersion(): void
    {
        self::assertTrue(GeneralHelper::isMinimumTypo3Version(13));
    }

    public function testIsMinimumTypo3VersionReturnsFalseForFutureVersion(): void
    {
        self::assertFalse(GeneralHelper::isMinimumTypo3Version(999));
    }

    public function testIsExtensionFeatureEnabledReturnsFalseForUnknownPath(): void
    {
        self::assertFalse(GeneralHelper::isExtensionFeatureEnabled('nonexistent/path'));
    }
}
