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

use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\ContextItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Toolbar;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * ContextItemTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ContextItemTest extends TestCase
{
    protected function setUp(): void
    {
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
        $item = new ContextItem(new ExtensionConfiguration());

        self::assertFalse($item->checkAccess());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        $item = new ContextItem(new ExtensionConfiguration());

        self::assertFalse($item->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        $item = new ContextItem(new ExtensionConfiguration());

        self::assertSame('', $item->getDropDown());
    }

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        $item = new ContextItem(new ExtensionConfiguration());

        self::assertSame([], $item->getAdditionalAttributes());
    }

    public function testHasDropDownReturnsTrueWhenDescriptionIsSet(): void
    {
        $this->setToolbar(['description' => 'DB synced nightly from live']);

        self::assertTrue((new ContextItem(new ExtensionConfiguration()))->hasDropDown());
    }

    public function testGetDropDownContainsEscapedDescription(): void
    {
        $this->setToolbar(['description' => 'Data <b>overwritten</b> nightly']);

        $dropDown = (new ContextItem(new ExtensionConfiguration()))->getDropDown();

        self::assertStringContainsString('Data &lt;b&gt;overwritten&lt;/b&gt; nightly', $dropDown);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function setToolbar(array $configuration): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [Toolbar::class => $configuration];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }
}
