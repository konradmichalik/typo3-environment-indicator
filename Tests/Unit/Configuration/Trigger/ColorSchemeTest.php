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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Trigger;

use InvalidArgumentException;
use KonradMichalik\Ttt\Attribute\WithBackendUser;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ColorScheme;
use PHPUnit\Framework\TestCase;

/**
 * ColorSchemeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ColorSchemeTest extends TestCase
{
    public function testCheckReturnsFalseWhenNoBackendUser(): void
    {
        $trigger = new ColorScheme('dark');

        self::assertFalse($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsTrueWhenSchemeMatches(): void
    {
        $GLOBALS['BE_USER']->uc['colorScheme'] = 'dark';

        $trigger = new ColorScheme('dark');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsFalseWhenSchemeDoesNotMatch(): void
    {
        $GLOBALS['BE_USER']->uc['colorScheme'] = 'light';

        $trigger = new ColorScheme('dark');

        self::assertFalse($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsTrueWhenAnyOfTheGivenSchemesMatches(): void
    {
        $GLOBALS['BE_USER']->uc['colorScheme'] = 'auto';

        $trigger = new ColorScheme('light', 'auto');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckTreatsMissingUserSettingAsAuto(): void
    {
        unset($GLOBALS['BE_USER']->uc['colorScheme']);

        $trigger = new ColorScheme('auto');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckDoesNotTreatMissingUserSettingAsLight(): void
    {
        unset($GLOBALS['BE_USER']->uc['colorScheme']);

        $trigger = new ColorScheme('light');

        self::assertFalse($trigger->check());
    }

    public function testConstructorRejectsUnknownScheme(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1786320000);

        new ColorScheme('sepia');
    }
}
