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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Utility;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ContextUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;

/**
 * ContextUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
class ContextUtilityTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    public function testGetColorReturnsTransparentWhenNoConfiguration(): void
    {
        $contextUtility = new ContextUtility();
        $color = $contextUtility->getColor();
        self::assertEquals('transparent', $color);
    }

    public function testGetDescriptionReturnsEmptyStringWhenNoConfiguration(): void
    {
        self::assertSame('', (new ContextUtility())->getDescription());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        Configuration\Indicator\Frontend\Hint::class => ['description' => '  Staging — data synced nightly  '],
    ]]]])]
    public function testGetDescriptionReturnsConfiguredDescription(): void
    {
        self::assertSame('Staging — data synced nightly', (new ContextUtility())->getDescription());
    }

    public function testGetTextColorReturnsColorUtilityResult(): void
    {
        $contextUtility = new ContextUtility();
        $textColor = $contextUtility->getTextColor();
        self::assertStringStartsWith('rgba(', $textColor);
    }

    #[DataProvider('positionClassDataProvider')]
    public function testGetPositionClassMapsConfiguredCornerToModifierClass(?string $configured, string $expected): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Configuration\Indicator\Frontend\Hint::class => null === $configured ? [] : ['position' => $configured],
        ];

        self::assertSame($expected, (new ContextUtility())->getPositionClass());
    }

    /**
     * @return array<string, array{0: string|null, 1: string}>
     */
    public static function positionClassDataProvider(): array
    {
        return [
            'top left' => ['top left', 'technical-context--top-left'],
            'top right' => ['top right', 'technical-context--top-right'],
            'bottom left' => ['bottom left', 'technical-context--bottom-left'],
            'bottom right' => ['bottom right', 'technical-context--bottom-right'],
            'mixed case is normalized' => ['Bottom Right', 'technical-context--bottom-right'],
            'extra whitespace is normalized' => ['  bottom   right ', 'technical-context--bottom-right'],
            'unconfigured falls back to the documented default' => [null, 'technical-context--top-left'],
            'swapped axes fall back instead of producing a broken class' => ['left top', 'technical-context--top-left'],
            'unknown value falls back' => ['centre', 'technical-context--top-left'],
        ];
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        Configuration\Indicator\Frontend\Hint::class => ['text' => 'My Environment'],
    ]]]])]
    public function testGetTitleReturnsConfiguredText(): void
    {
        self::assertSame('My Environment', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenNoRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenRoutingIsNotPageArguments(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(null);

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenSiteFinderIsMissing(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(new PageArguments(1, '0', []));

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    private function createRequestWithRouting(?PageArguments $routing): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')->with('routing')->willReturn($routing);

        return $request;
    }
}
