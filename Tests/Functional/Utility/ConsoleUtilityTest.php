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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Utility;

use KonradMichalik\Ttt\Attribute\Typo3ConfVarsSentinel;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\Console;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ConsoleUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ConsoleUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConsoleUtilityTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testBadgeTextResolvesTheContextPlaceholderBehindTheFormatDirective(): void
    {
        $this->configure(['text' => '%context%', 'color' => '#bd593a']);

        self::assertSame('%cTesting', (new ConsoleUtility())->getBadgeText());
    }

    public function testBadgeTextIsTrimmed(): void
    {
        $this->configure(['text' => '  STAGING  ']);

        self::assertSame('%cSTAGING', (new ConsoleUtility())->getBadgeText());
    }

    public function testPercentSignInTextIsEscapedAsFormatDirective(): void
    {
        $this->configure(['text' => '100% TEST']);

        self::assertSame('%c100%% TEST', (new ConsoleUtility())->getBadgeText());
    }

    public function testStyleUsesTheConfiguredColor(): void
    {
        $this->configure(['text' => 'DEV', 'color' => '#bd593a']);

        self::assertStringContainsString('background:#bd593a', (new ConsoleUtility())->getStyle());
    }

    public function testStyleFallsBackToNeutralColor(): void
    {
        $this->configure(['text' => 'DEV']);

        self::assertStringContainsString('background:#767676', (new ConsoleUtility())->getStyle());
    }

    public function testStyleDerivesTextColorFromBackgroundColor(): void
    {
        $this->configure(['text' => 'DEV', 'color' => '#ffffff']);

        // Optimal text color on white has to be dark, not the white default.
        self::assertStringContainsString('color:rgba(0, 0, 0', (new ConsoleUtility())->getStyle());
    }

    public function testEmptyTextDisablesTheBadge(): void
    {
        $this->configure(['text' => '', 'color' => '#bd593a']);

        self::assertSame('', (new ConsoleUtility())->getBadgeText());
    }

    public function testInactiveIndicatorProducesNoBadgeText(): void
    {
        $this->configure(null);

        self::assertSame('', (new ConsoleUtility())->getBadgeText());
    }

    /**
     * @param array<string, string>|null $indicatorConfiguration Null registers no indicator at all
     */
    private function configure(?array $indicatorConfiguration): void
    {
        $this->setTypo3ConfVars([
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => null === $indicatorConfiguration
                    ? Typo3ConfVarsSentinel::Unset
                    : [Console::class => $indicatorConfiguration],
                'resolved' => true,
            ]],
        ]);
    }
}
