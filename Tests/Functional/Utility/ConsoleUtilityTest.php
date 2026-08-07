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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Console;
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

    public function testScriptContainsResolvedContextAndBadgeStyle(): void
    {
        $this->configure(['text' => '%context%', 'color' => '#bd593a']);

        $script = (new ConsoleUtility())->getScript();

        self::assertStringStartsWith('console.info(', $script);
        self::assertStringContainsString('%cTesting', $script);
        self::assertStringContainsString('background:#bd593a', $script);
        self::assertStringContainsString('border-radius:3px', $script);
    }

    public function testTextColorIsDerivedFromBackgroundColor(): void
    {
        $this->configure(['text' => 'DEV', 'color' => '#ffffff']);

        // Optimal text color on white has to be dark, not the white default.
        self::assertStringContainsString('color:rgba(0, 0, 0', (new ConsoleUtility())->getScript());
    }

    public function testPercentSignInTextIsEscapedAsFormatDirective(): void
    {
        $this->configure(['text' => '100% TEST', 'color' => '#bd593a']);

        self::assertStringContainsString('%c100%% TEST', (new ConsoleUtility())->getScript());
    }

    public function testHtmlSpecialCharactersAreHexEscaped(): void
    {
        $this->configure(['text' => '</script><img src=x onerror=alert(1)>', 'color' => '#bd593a']);

        $script = (new ConsoleUtility())->getScript();

        // No raw angle brackets survive, so the payload cannot terminate the
        // <script> element the statement is embedded in.
        self::assertStringNotContainsString('<', $script);
        self::assertStringNotContainsString('>', $script);
        self::assertStringContainsString('\\u003C', $script);
    }

    public function testQuotesCannotBreakOutOfTheStringLiteral(): void
    {
        $this->configure(['text' => '");alert(1);//', 'color' => '#bd593a']);

        $script = (new ConsoleUtility())->getScript();

        // The payload stays inside the string literal as data: the quote is
        // escaped, so the sequence that would close it never appears verbatim.
        self::assertStringContainsString('\\u0022', $script);
        self::assertStringNotContainsString('");alert', $script);
    }

    public function testEmptyTextProducesNoScript(): void
    {
        $this->configure(['text' => '', 'color' => '#bd593a']);

        self::assertSame('', (new ConsoleUtility())->getScript());
    }

    public function testInactiveIndicatorProducesNoScript(): void
    {
        $this->configure(null);

        self::assertSame('', (new ConsoleUtility())->getScript());
    }

    public function testMissingColorFallsBackToNeutralBadge(): void
    {
        $this->configure(['text' => 'DEV']);

        self::assertStringContainsString('background:#767676', (new ConsoleUtility())->getScript());
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
