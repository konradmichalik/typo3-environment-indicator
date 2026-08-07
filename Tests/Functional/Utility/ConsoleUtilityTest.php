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

    public function testTextResolvesTheContextPlaceholder(): void
    {
        $this->configure(['text' => '%context%', 'color' => '#bd593a']);

        self::assertSame('Testing', (new ConsoleUtility())->getText());
    }

    public function testTextIsTrimmed(): void
    {
        $this->configure(['text' => '  STAGING  ']);

        self::assertSame('STAGING', (new ConsoleUtility())->getText());
    }

    public function testConfiguredColorIsUsed(): void
    {
        $this->configure(['text' => 'DEV', 'color' => '#bd593a']);

        self::assertSame('#bd593a', (new ConsoleUtility())->getColor());
    }

    public function testMissingColorFallsBackToNeutral(): void
    {
        $this->configure(['text' => 'DEV']);

        self::assertSame('#767676', (new ConsoleUtility())->getColor());
    }

    public function testTextColorIsDerivedFromBackgroundColor(): void
    {
        $this->configure(['text' => 'DEV', 'color' => '#ffffff']);

        // Optimal text color on white has to be dark, not the white default.
        self::assertStringStartsWith('rgba(0, 0, 0', (new ConsoleUtility())->getTextColor());
    }

    public function testEmptyTextDisablesTheBadge(): void
    {
        $this->configure(['text' => '', 'color' => '#bd593a']);

        self::assertSame('', (new ConsoleUtility())->getText());
    }

    public function testInactiveIndicatorProducesNoText(): void
    {
        $this->configure(null);

        self::assertSame('', (new ConsoleUtility())->getText());
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
