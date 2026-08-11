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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Backend\Form\Element;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Backend\Form\Element\ThemeInfoElement;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function chr;
use function define;
use function defined;

/**
 * ThemeInfoElementTest.
 *
 * getLanguageService()/getBackendUser() on AbstractFormElement read the
 * $GLOBALS['LANG']/$GLOBALS['BE_USER'] singletons directly, so those are
 * stubbed here instead of going through a Functional bootstrap.
 *
 * The EXTENSIONS[EXT_KEY] override must stay an array, not null:
 * ExtensionConfiguration::hasConfiguration() checks it via isset(), which
 * is false for null and would make ->get() fall back to a real
 * PackageManager lookup that isn't available in this Unit bootstrap.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => []]])]
final class ThemeInfoElementTest extends TestCase
{
    protected function setUp(): void
    {
        // Normally defined by TYPO3's full bootstrap (SystemEnvironmentBuilder),
        // which the Unit test bootstrap does not run.
        defined('LF') || define('LF', chr(10));

        $languageService = $this->createStub(LanguageService::class);
        $languageService->method('sL')->willReturn('Theme info message');
        $GLOBALS['LANG'] = $languageService;

        $backendUser = $this->createStub(BackendUserAuthentication::class);
        $backendUser->method('shallDisplayDebugInformation')->willReturn(false);
        $GLOBALS['BE_USER'] = $backendUser;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['LANG'], $GLOBALS['BE_USER']);
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
    public function testRenderReturnsUnmodifiedResultArrayWhenNotApplicable(): void
    {
        $this->mockTypo3Version(13);
        $element = new ThemeInfoElement();
        $element->setData([]);

        $result = $element->render();

        self::assertSame('', $result['html']);
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['theme' => '1']]]])]
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => ['color' => '#bd593a']],
        'resolved' => true,
    ]]])]
    public function testRenderReturnsInfoBoxWhenApplicable(): void
    {
        $this->mockTypo3Version(14);
        $element = new ThemeInfoElement();
        $element->setData(['parameterArray' => ['fieldConf' => ['label' => 'Theme']]]);

        $result = $element->render();

        self::assertStringContainsString('alert alert-info', $result['html']);
        self::assertStringContainsString('Theme info message', $result['html']);
        self::assertStringContainsString('<fieldset>', $result['html']);
    }

    private function mockTypo3Version(int $majorVersion): void
    {
        $version = $this->createStub(Typo3Version::class);
        $version->method('getMajorVersion')->willReturn($majorVersion);
        GeneralUtility::addInstance(Typo3Version::class, $version);
    }
}
