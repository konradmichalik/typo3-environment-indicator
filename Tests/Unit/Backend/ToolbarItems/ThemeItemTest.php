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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Backend\ToolbarItems;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\ThemeItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Theme;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ThemeItemTest.
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
final class ThemeItemTest extends TestCase
{
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
    public function testCheckAccessReturnsFalseWhenNotApplicable(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertFalse($item->checkAccess());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['theme' => '1']]],
        'EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Theme::class => ['color' => '#bd593a']],
            'resolved' => true,
        ]],
    ])]
    public function testCheckAccessReturnsTrueWhenApplicable(): void
    {
        $this->mockTypo3Version(14);
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertTrue($item->checkAccess());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertFalse($item->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertSame('', $item->getDropDown());
    }

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertSame([], $item->getAdditionalAttributes());
    }

    public function testGetIndexReturnsZero(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertSame(0, $item->getIndex());
    }

    public function testImplementsToolbarItemInterface(): void
    {
        $item = new ThemeItem($this->createStub(PageRenderer::class));

        self::assertInstanceOf(ToolbarItemInterface::class, $item);
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
    public function testGetItemReturnsEmptyStringWhenNoColorConfigured(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => ['color' => 'not-a-color']],
        'resolved' => true,
    ]]])]
    public function testGetItemReturnsEmptyStringWhenColorInvalid(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => ['color' => '#bd593a']],
        'resolved' => true,
    ]]])]
    public function testGetItemInjectsCssWithDefaultsWhenScaffoldFlagsOmitted(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_theme',
                self::logicalAnd(
                    self::stringContains('--token-color-primary-base: #bd593a;'),
                    self::stringContains('--typo3-color-neutral-mix: 15%;'),
                    self::stringContains('--typo3-scaffold-header-bg:'),
                    self::stringContains('--typo3-scaffold-sidebar-bg:'),
                    self::stringContains('[data-theme] .scaffold-sidebar'),
                ),
            );
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => ['color' => '#abc', 'neutralMix' => 'invalid']],
        'resolved' => true,
    ]]])]
    public function testGetItemFallsBackToDefaultNeutralMixWhenInvalid(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_theme',
                self::logicalAnd(
                    self::stringContains('--token-color-primary-base: #abc;'),
                    self::stringContains('--typo3-color-neutral-mix: 15%;'),
                ),
            );
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => [
            'color' => '#bd593a',
            'neutralMix' => '25.5%',
            'scaffoldHeader' => false,
            'scaffoldSidebar' => true,
        ]],
        'resolved' => true,
    ]]])]
    public function testGetItemOmitsHeaderCssWhenScaffoldHeaderDisabled(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_theme',
                self::logicalAnd(
                    self::stringContains('--typo3-color-neutral-mix: 25.5%;'),
                    self::logicalNot(self::stringContains('--typo3-scaffold-header-bg:')),
                    self::stringContains('--typo3-scaffold-sidebar-bg:'),
                ),
            );
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Theme::class => [
            'color' => '#bd593a',
            'scaffoldHeader' => true,
            'scaffoldSidebar' => false,
        ]],
        'resolved' => true,
    ]]])]
    public function testGetItemOmitsSidebarCssWhenScaffoldSidebarDisabled(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_theme',
                self::logicalAnd(
                    self::stringContains('--typo3-scaffold-header-bg:'),
                    self::logicalNot(self::stringContains('--typo3-scaffold-sidebar-bg:')),
                    self::logicalNot(self::stringContains('[data-theme] .scaffold-sidebar')),
                ),
            );
        $item = new ThemeItem($pageRenderer);

        self::assertSame('', $item->getItem());
    }

    private function mockTypo3Version(int $majorVersion): void
    {
        $version = $this->createStub(Typo3Version::class);
        $version->method('getMajorVersion')->willReturn($majorVersion);
        GeneralUtility::addInstance(Typo3Version::class, $version);
    }
}
