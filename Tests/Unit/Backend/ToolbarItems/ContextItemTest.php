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
    public function testCheckAccessReturnsTrue(): void
    {
        $extensionConfig = $this->createStub(ExtensionConfiguration::class);
        $item = new ContextItem($extensionConfig);

        self::assertTrue($item->checkAccess());
    }

    public function testHasDropDownReturnsFalse(): void
    {
        $extensionConfig = $this->createStub(ExtensionConfiguration::class);
        $item = new ContextItem($extensionConfig);

        self::assertFalse($item->hasDropDown());
    }

    public function testGetDropDownReturnsEmptyString(): void
    {
        $extensionConfig = $this->createStub(ExtensionConfiguration::class);
        $item = new ContextItem($extensionConfig);

        self::assertSame('', $item->getDropDown());
    }

    public function testGetAdditionalAttributesReturnsEmptyArray(): void
    {
        $extensionConfig = $this->createStub(ExtensionConfiguration::class);
        $item = new ContextItem($extensionConfig);

        self::assertSame([], $item->getAdditionalAttributes());
    }
}
