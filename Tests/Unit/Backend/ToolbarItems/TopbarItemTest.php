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
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\TopbarItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * TopbarItemTest.
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
    'EXTCONF' => [Configuration::EXT_KEY => ['current' => null]],
    'EXTENSIONS' => [Configuration::EXT_KEY => []],
])]
class TopbarItemTest extends TestCase
{
    public function testConstructorWithExtensionConfiguration(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertInstanceOf(TopbarItem::class, $topbarItem);
    }

    public function testCheckAccessReturnsFalseWhenFeatureDisabled(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertFalse($topbarItem->checkAccess());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertFalse($topbarItem->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertEquals('', $topbarItem->getDropDown());
    }

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertEquals([], $topbarItem->getAdditionalAttributes());
    }

    public function testGetIndexReturnsZero(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertEquals(0, $topbarItem->getIndex());
    }

    public function testImplementsToolbarItemInterface(): void
    {
        $pageRenderer = $this->createStub(PageRenderer::class);
        $topbarItem = new TopbarItem(new ExtensionConfiguration(), $pageRenderer);
        self::assertInstanceOf(ToolbarItemInterface::class, $topbarItem);
    }
}
