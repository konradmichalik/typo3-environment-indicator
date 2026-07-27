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
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * ContextItemTest.
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
}
