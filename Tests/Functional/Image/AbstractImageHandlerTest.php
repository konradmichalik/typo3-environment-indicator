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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Image;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Image\FaviconHandler;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TriangleModifier;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

use function extension_loaded;

/**
 * AbstractImageHandlerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AbstractImageHandlerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    private string $testImagePath;

    protected function setUp(): void
    {
        parent::setUp();

        if (!extension_loaded('gd')) {
            self::markTestSkipped('GD extension is not available.');
        }

        $fixtureDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        GeneralUtility::mkdir_deep($fixtureDir);
        $this->testImagePath = $fixtureDir.'test_favicon.png';

        $gd = imagecreatetruecolor(16, 16);
        $red = imagecolorallocate($gd, 255, 0, 0);
        imagefill($gd, 0, 0, $red);
        imagepng($gd, $this->testImagePath);
        imagedestroy($gd);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][Favicon::class] = [
            '_path' => 'typo3temp/assets/environment-indicator-test/',
        ];
    }

    protected function tearDown(): void
    {
        $fixtureDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        if (is_dir($fixtureDir)) {
            GeneralUtility::rmdir($fixtureDir, true);
        }

        parent::tearDown();
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][Favicon::class]);
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current']);
    }

    public function testProcessReturnsOriginalPathWhenFileDoesNotExist(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process('/nonexistent/path/favicon.png');

        self::assertSame('/nonexistent/path/favicon.png', $result);
    }

    public function testProcessReturnsOriginalPathWhenIndicatorNotCurrent(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];

        $handler = new FaviconHandler();
        $result = $handler->process($this->testImagePath);

        self::assertSame($this->testImagePath, $result);
    }

    public function testProcessReturnsOriginalPathWhenNoModifiersConfigured(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process($this->testImagePath);

        self::assertSame($this->testImagePath, $result);
    }

    public function testProcessReturnsModifiedPathWhenModifierIsConfigured(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process($this->testImagePath);

        self::assertNotSame($this->testImagePath, $result);
        self::assertStringEndsWith('.png', $result);
    }

    public function testProcessReturnsDifferentPathsForDifferentModifierConfiguration(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];
        $firstResult = (new FaviconHandler())->process($this->testImagePath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#00ff00'])],
        ];
        $secondResult = (new FaviconHandler())->process($this->testImagePath);

        self::assertNotSame($firstResult, $secondResult);
    }

    public function testProcessReturnsCachedPathOnSecondCall(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $firstResult = $handler->process($this->testImagePath);
        $secondResult = $handler->process($this->testImagePath);

        self::assertSame($firstResult, $secondResult);
    }

    public function testProcessHandlesSvgFile(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_favicon.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test.svg', $svgPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process($svgPath);

        self::assertStringEndsWith('.png', $result);
    }

    public function testProcessHandlesSvgFileWithViewBoxOnly(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_no_dims.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test-no-dims.svg', $svgPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process($svgPath);

        self::assertStringEndsWith('.png', $result);
    }

    public function testProcessReturnsOriginalPathForUnsupportedFormat(): void
    {
        $unsupportedDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $unsupportedPath = $unsupportedDir.'test_favicon.txt';
        GeneralUtility::mkdir_deep($unsupportedDir);
        file_put_contents($unsupportedPath, 'not an image');

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000'])],
        ];

        $handler = new FaviconHandler();
        $result = $handler->process($unsupportedPath);

        self::assertSame($unsupportedPath, $result);
    }
}
