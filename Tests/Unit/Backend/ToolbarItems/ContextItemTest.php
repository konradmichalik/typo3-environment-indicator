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
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\ContextItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Toolbar;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * ContextItemTest.
 *
 * The EXTENSIONS[EXT_KEY] override must stay an empty array, not null:
 * ExtensionConfiguration::hasConfiguration() checks it via isset(), which
 * is false for null and would make ->get() fall back to a real
 * PackageManager lookup that isn't available in this Unit bootstrap.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => []]])]
final class ContextItemTest extends TestCase
{
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

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Toolbar::class => ['description' => 'DB synced nightly from live']],
        'resolved' => true,
    ]]])]
    public function testHasDropDownReturnsTrueWhenDescriptionIsSet(): void
    {
        self::assertTrue((new ContextItem(new ExtensionConfiguration()))->hasDropDown());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Toolbar::class => ['description' => 'Data <b>overwritten</b> nightly']],
        'resolved' => true,
    ]]])]
    public function testGetDropDownContainsEscapedDescription(): void
    {
        $dropDown = (new ContextItem(new ExtensionConfiguration()))->getDropDown();

        self::assertStringContainsString('Data &lt;b&gt;overwritten&lt;/b&gt; nightly', $dropDown);
    }
}
