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
 * The EXTENSIONS[EXT_KEY] override must stay an empty array, not null:
 * ExtensionConfiguration::hasConfiguration() checks it via isset(), which
 * is false for null and would make ->get() fall back to a real
 * PackageManager lookup that isn't available in this Unit bootstrap.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars([
    'EXTCONF' => [Configuration::EXT_KEY => ['current' => null, 'resolved' => true]],
    'EXTENSIONS' => [Configuration::EXT_KEY => []],
])]
class PageTitleItemTest extends TestCase
{
    public function testCheckAccessReturnsFalseWhenFeatureDisabled(): void
    {
        self::assertFalse($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['pageTitle' => true]]]])]
    public function testCheckAccessReturnsFalseWhenIndicatorNotResolved(): void
    {
        self::assertFalse($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['pageTitle' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [PageTitle::class => []]]],
    ])]
    public function testCheckAccessReturnsTrueWhenEnabledAndResolved(): void
    {
        self::assertTrue($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['pageTitle' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [PageTitle::class => ['prefix' => '[STG] ']]]],
    ])]
    public function testGetItemInjectsScriptWithPrefix(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                Configuration::EXT_KEY.'_pagetitle',
                self::callback(static fn (string $js): bool => str_contains($js, '[STG] ')),
            );

        self::assertSame('', $this->buildItem($pageRenderer)->getItem());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['pageTitle' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [PageTitle::class => []]]],
    ])]
    public function testGetItemInjectsNothingWithoutPrefixOrSuffix(): void
    {
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

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        self::assertSame([], $this->buildItem()->getAdditionalAttributes());
    }

    public function testImplementsToolbarItemInterface(): void
    {
        self::assertInstanceOf(ToolbarItemInterface::class, $this->buildItem());
    }

    private function buildItem(?PageRenderer $pageRenderer = null): PageTitleItem
    {
        return new PageTitleItem(new ExtensionConfiguration(), $pageRenderer ?? $this->createStub(PageRenderer::class));
    }
}
