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

use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\PageTitleItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * PageTitleItemTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class PageTitleItemTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [];
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
        );
    }

    public function testCheckAccessReturnsFalseWhenFeatureDisabled(): void
    {
        self::assertFalse($this->buildItem()->checkAccess());
    }

    public function testCheckAccessReturnsFalseWhenIndicatorNotResolved(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = ['backend' => ['pageTitle' => true]];

        self::assertFalse($this->buildItem()->checkAccess());
    }

    public function testCheckAccessReturnsTrueWhenEnabledAndResolved(): void
    {
        $this->enableIndicator();

        self::assertTrue($this->buildItem()->checkAccess());
    }

    public function testGetItemInjectsScriptWithPrefix(): void
    {
        $this->enableIndicator(['prefix' => '[STG] ']);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                Configuration::EXT_KEY.'_pagetitle',
                self::callback(static fn (string $js): bool => str_contains($js, '[STG] ')),
            );

        self::assertSame('', $this->buildItem($pageRenderer)->getItem());
    }

    public function testGetItemInjectsNothingWithoutPrefixOrSuffix(): void
    {
        $this->enableIndicator([]);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addJsInlineCode');

        self::assertSame('', $this->buildItem($pageRenderer)->getItem());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        self::assertFalse($this->buildItem()->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        self::assertSame('', $this->buildItem()->getDropDown());
    }

    public function testGetIndexReturnsZero(): void
    {
        self::assertSame(0, $this->buildItem()->getIndex());
    }

    public function testImplementsToolbarItemInterface(): void
    {
        self::assertInstanceOf(ToolbarItemInterface::class, $this->buildItem());
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function enableIndicator(array $configuration = []): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = ['backend' => ['pageTitle' => true]];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            PageTitle::class => $configuration,
        ];
    }

    private function buildItem(?PageRenderer $pageRenderer = null): PageTitleItem
    {
        return new PageTitleItem(new ExtensionConfiguration(), $pageRenderer ?? $this->createStub(PageRenderer::class));
    }
}
