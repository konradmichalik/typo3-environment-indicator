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

use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\TopbarItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * TopbarItemTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TopbarItemTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
    }

    public function testConstructorWithExtensionConfiguration(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertInstanceOf(TopbarItem::class, $topbarItem);
    }

    public function testCheckAccessReturnsTrue(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertTrue($topbarItem->checkAccess());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertFalse($topbarItem->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertEquals('', $topbarItem->getDropDown());
    }

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertEquals([], $topbarItem->getAdditionalAttributes());
    }

    public function testGetIndexReturnsZero(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertEquals(0, $topbarItem->getIndex());
    }

    public function testImplementsToolbarItemInterface(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $topbarItem = new TopbarItem($extensionConfiguration);
        self::assertInstanceOf(ToolbarItemInterface::class, $topbarItem);
    }
}
