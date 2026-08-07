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
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\ConsoleItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\Console;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ConsoleUtility;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;

use function str_contains;

/**
 * ConsoleItemTest.
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
class ConsoleItemTest extends TestCase
{
    public function testCheckAccessReturnsFalseWhenFeatureDisabled(): void
    {
        self::assertFalse($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['console' => true]]]])]
    public function testCheckAccessReturnsFalseWhenIndicatorNotResolved(): void
    {
        self::assertFalse($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['console' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [Console::class => []]]],
    ])]
    public function testCheckAccessReturnsTrueWhenEnabledAndResolved(): void
    {
        self::assertTrue($this->buildItem()->checkAccess());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['console' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [Console::class => ['text' => 'STAGING', 'color' => '#2f9c91']]]],
    ])]
    public function testGetItemInjectsBadgeScript(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                Configuration::EXT_KEY.'_console',
                self::callback(static fn (string $js): bool => str_contains($js, 'console.info(')
                    && str_contains($js, '%cSTAGING')
                    && str_contains($js, 'background:#2f9c91')),
            );

        self::assertSame('', $this->buildItem($pageRenderer)->getItem());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['console' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [Console::class => ['text' => '']]]],
    ])]
    public function testGetItemInjectsNothingWithoutText(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addJsInlineCode');

        self::assertSame('', $this->buildItem($pageRenderer)->getItem());
    }

    #[WithTypo3ConfVars([
        'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['console' => true]]],
        'EXTCONF' => [Configuration::EXT_KEY => ['current' => [Console::class => ['text' => '</script><img src=x onerror=alert(1)>']]]],
    ])]
    public function testInjectedScriptCannotBreakOutOfTheScriptElement(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addJsInlineCode')
            ->with(
                self::anything(),
                self::callback(static fn (string $js): bool => !str_contains($js, '<') && !str_contains($js, '>')),
            );

        $this->buildItem($pageRenderer)->getItem();
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

    private function buildItem(?PageRenderer $pageRenderer = null): ConsoleItem
    {
        return new ConsoleItem(
            new ExtensionConfiguration(),
            $pageRenderer ?? $this->createStub(PageRenderer::class),
            new ConsoleUtility(),
        );
    }
}
