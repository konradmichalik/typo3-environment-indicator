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

use KonradMichalik\Ttt\Fixture\ImageFixtures;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Image\FaviconHandler;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\{OverlayModifier, ReplaceModifier, TriangleModifier};
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

use function extension_loaded;
use function imagecolorat;
use function imagecolorsforindex;
use function imagecreatefrompng;

/**
 * AbstractImageHandlerTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AbstractImageHandlerTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

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

        ImageFixtures::createPng(16, 16, [255, 0, 0], $this->testImagePath);

        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'defaults' => [Favicon::class => ['_path' => 'typo3temp/assets/environment-indicator-test/']],
            // Mark resolution as already done so the resolver never reprocesses the
            // registered presets (e.g. the "Testing" context favicon) on top of the
            // 'current' fixture each test below sets up.
            'resolved' => true,
            // Shared baseline for most tests; the few that need a different
            // "current" configuration reassign $GLOBALS directly in the method
            // body, which simply overrides this at runtime.
            'current' => [Favicon::class => [new TriangleModifier(['color' => '#ff0000'])]],
        ]]]);
    }

    protected function tearDown(): void
    {
        $fixtureDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        if (is_dir($fixtureDir)) {
            GeneralUtility::rmdir($fixtureDir, true);
        }

        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testProcessReturnsOriginalPathWhenFileDoesNotExist(): void
    {
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

    public function testProcessIncludesRawNonModifierConfigurationEntriesInGeneratedFilename(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000']), '_meta' => 'first'],
        ];
        $firstResult = (new FaviconHandler())->process($this->testImagePath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new TriangleModifier(['color' => '#ff0000']), '_meta' => 'second'],
        ];
        $secondResult = (new FaviconHandler())->process($this->testImagePath);

        self::assertNotSame($firstResult, $secondResult, 'the raw "_meta" entry must be part of the hashed filename even though it is not a ModifierInterface');
    }

    public function testProcessReturnsCachedPathOnSecondCall(): void
    {
        $handler = new FaviconHandler();
        $firstResult = $handler->process($this->testImagePath);
        $secondResult = $handler->process($this->testImagePath);

        self::assertSame($firstResult, $secondResult);
    }

    public function testProcessLeavesNoTemporaryFilesBehind(): void
    {
        $result = (new FaviconHandler())->process($this->testImagePath);

        self::assertFileExists(Environment::getPublicPath().'/'.$result);

        $folder = Environment::getPublicPath().'/typo3temp/assets/environment-indicator-test/';
        self::assertSame([], glob($folder.'.tmp-*') ?: []);
    }

    public function testProcessHandlesSvgFile(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_favicon.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test.svg', $svgPath);

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

        $handler = new FaviconHandler();
        $result = $handler->process($svgPath);

        self::assertStringEndsWith('.png', $result);
    }

    public function testProcessReturnsOriginalPathWhenSvgIsMalformed(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_malformed.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test-malformed.svg', $svgPath);

        $handler = new FaviconHandler();
        $result = $handler->process($svgPath);

        self::assertSame($svgPath, $result);
    }

    public function testConvertSvgToPngLeavesNoTemporaryFilesBehind(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_favicon.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test.svg', $svgPath);

        $result = (new FaviconHandler())->process($svgPath);

        self::assertFileExists(Environment::getPublicPath().'/'.$result);

        $processedFolder = Environment::getPublicPath().'/typo3temp/assets/environment-indicator-test/processed/';
        self::assertSame([], glob($processedFolder.'.tmp-*') ?: []);
    }

    public function testConvertSvgToPngReusesCachedIntermediateOnRetryAfterFinalOutputWasLost(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $svgPath = $svgDir.'test_favicon_retry.svg';
        GeneralUtility::mkdir_deep($svgDir);
        copy(__DIR__.'/Fixtures/test.svg', $svgPath);

        $firstResult = (new FaviconHandler())->process($svgPath);
        $finalOutputPath = Environment::getPublicPath().'/'.$firstResult;
        self::assertFileExists($finalOutputPath);

        // Simulate the final output being lost (e.g. a prior attempt failed
        // after the intermediate SVG->PNG cache was already written) so the
        // retry below must reuse that intermediate cache instead of
        // re-rasterizing it.
        unlink($finalOutputPath);
        self::assertFileDoesNotExist($finalOutputPath);

        $secondResult = (new FaviconHandler())->process($svgPath);

        self::assertSame($firstResult, $secondResult);
        self::assertFileExists(Environment::getPublicPath().'/'.$secondResult);
    }

    public function testConvertIcoToPngLeavesNoTemporaryFilesBehind(): void
    {
        $icoDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $icoPath = $icoDir.'test_favicon.ico';
        GeneralUtility::mkdir_deep($icoDir);
        copy(__DIR__.'/../../../Resources/Public/Icons/favicon.ico', $icoPath);

        $result = (new FaviconHandler())->process($icoPath);

        self::assertFileExists(Environment::getPublicPath().'/'.$result);

        $processedFolder = Environment::getPublicPath().'/typo3temp/assets/environment-indicator-test/processed/';
        self::assertSame([], glob($processedFolder.'.tmp-*') ?: []);
    }

    public function testConvertIcoToPngCreatesProcessedFolderWhenMissing(): void
    {
        $processedFolder = Environment::getPublicPath().'/typo3temp/assets/environment-indicator-test/processed/';
        if (is_dir($processedFolder)) {
            GeneralUtility::rmdir($processedFolder, true);
        }
        self::assertDirectoryDoesNotExist($processedFolder);

        $icoDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $icoPath = $icoDir.'test_favicon_mkdir.ico';
        GeneralUtility::mkdir_deep($icoDir);
        copy(__DIR__.'/../../../Resources/Public/Icons/favicon.ico', $icoPath);

        (new FaviconHandler())->process($icoPath);

        self::assertDirectoryExists($processedFolder);
    }

    public function testConvertIcoToPngReusesCachedIntermediateOnRetryAfterFinalOutputWasLost(): void
    {
        $icoDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $icoPath = $icoDir.'test_favicon_retry.ico';
        GeneralUtility::mkdir_deep($icoDir);
        copy(__DIR__.'/../../../Resources/Public/Icons/favicon.ico', $icoPath);

        $firstResult = (new FaviconHandler())->process($icoPath);
        $finalOutputPath = Environment::getPublicPath().'/'.$firstResult;
        self::assertFileExists($finalOutputPath);

        // Simulate the final output being lost (e.g. a prior attempt failed
        // after the intermediate ICO->PNG cache was already written) so the
        // retry below must reuse that intermediate cache instead of
        // re-rendering it.
        unlink($finalOutputPath);
        self::assertFileDoesNotExist($finalOutputPath);

        $secondResult = (new FaviconHandler())->process($icoPath);

        self::assertSame($firstResult, $secondResult);
        self::assertFileExists(Environment::getPublicPath().'/'.$secondResult);
    }

    public function testProcessReturnsOriginalPathForUnsupportedFormat(): void
    {
        $unsupportedDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        $unsupportedPath = $unsupportedDir.'test_favicon.txt';
        GeneralUtility::mkdir_deep($unsupportedDir);
        file_put_contents($unsupportedPath, 'not an image');

        $handler = new FaviconHandler();
        $result = $handler->process($unsupportedPath);

        self::assertSame($unsupportedPath, $result);
    }

    public function testReplaceModifierSwapsOutTheOriginalImage(): void
    {
        $replacementPath = Environment::getPublicPath().'/typo3temp/assets/test-handler/replacement.png';
        ImageFixtures::createPng(16, 16, [0, 0, 255], $replacementPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new ReplaceModifier(['path' => $replacementPath])],
        ];

        $result = (new FaviconHandler())->process($this->testImagePath);

        self::assertSame([0, 0, 255], $this->getPixelColor(Environment::getPublicPath().'/'.$result));
    }

    public function testReplaceModifierKeepsOriginalImageWhenSvgReplacementIsMalformed(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        GeneralUtility::mkdir_deep($svgDir);
        $replacementPath = $svgDir.'test_malformed.svg';
        copy(__DIR__.'/Fixtures/test-malformed.svg', $replacementPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new ReplaceModifier(['path' => $replacementPath])],
        ];

        $result = (new FaviconHandler())->process($this->testImagePath);

        self::assertSame([255, 0, 0], $this->getPixelColor(Environment::getPublicPath().'/'.$result), 'original red favicon must be kept when the SVG replacement cannot be rasterized');
    }

    public function testReplaceModifierPreservesSvgTransparency(): void
    {
        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        GeneralUtility::mkdir_deep($svgDir);
        $replacementPath = $svgDir.'test_transparent.svg';
        copy(__DIR__.'/Fixtures/test-transparent.svg', $replacementPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new ReplaceModifier(['path' => $replacementPath])],
        ];

        $result = (new FaviconHandler())->process($this->testImagePath);
        $resultPath = Environment::getPublicPath().'/'.$result;

        self::assertSame(127, $this->getPixelAlpha($resultPath, 0, 0), 'left half of the SVG is transparent and must stay transparent');
        self::assertSame(0, $this->getPixelAlpha($resultPath, 12, 0), 'right half of the SVG is opaque white and must stay opaque');
    }

    public function testReplaceModifierPreservesSvgTransparencyWithImagickDriver(): void
    {
        if (!extension_loaded('imagick')) {
            self::markTestSkipped('Imagick extension is not available.');
        }

        $svgDir = Environment::getPublicPath().'/typo3temp/assets/test-handler/';
        GeneralUtility::mkdir_deep($svgDir);
        $replacementPath = $svgDir.'test_transparent_imagick.svg';
        copy(__DIR__.'/Fixtures/test-transparent.svg', $replacementPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]['general']['imageDriver'] = 'imagick';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new ReplaceModifier(['path' => $replacementPath])],
        ];

        $result = (new FaviconHandler())->process($this->testImagePath);
        $resultPath = Environment::getPublicPath().'/'.$result;

        self::assertSame(127, $this->getPixelAlpha($resultPath, 0, 0), 'left half of the SVG is transparent and must stay transparent with the Imagick driver');
        self::assertSame(0, $this->getPixelAlpha($resultPath, 12, 0), 'right half of the SVG is opaque white and must stay opaque with the Imagick driver');
    }

    public function testOverlayModifierPlacesOverlayOnTopOfImage(): void
    {
        $overlayPath = Environment::getPublicPath().'/typo3temp/assets/test-handler/overlay.png';
        ImageFixtures::createPng(8, 8, [0, 255, 0], $overlayPath);

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Favicon::class => [new OverlayModifier([
                'path' => $overlayPath,
                'size' => 0.5,
                'position' => 'top left',
                'padding' => 0,
            ])],
        ];

        $result = (new FaviconHandler())->process($this->testImagePath);
        $resultPath = Environment::getPublicPath().'/'.$result;

        self::assertSame([0, 255, 0], $this->getPixelColor($resultPath), 'overlay placed at top left must cover the original pixel there');
    }

    /**
     * @return array{int, int, int}
     */
    private function getPixelColor(string $path): array
    {
        $image = imagecreatefrompng($path);
        self::assertNotFalse($image);

        $rgb = imagecolorsforindex($image, imagecolorat($image, 0, 0));

        return [$rgb['red'], $rgb['green'], $rgb['blue']];
    }

    private function getPixelAlpha(string $path, int $x, int $y): int
    {
        $image = imagecreatefrompng($path);
        self::assertNotFalse($image);

        $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y));

        return $rgb['alpha'];
    }
}
