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

use KonradMichalik\Ttt\Attribute\WithBackendUser;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Theme;
use PHPUnit\Framework\TestCase;

/**
 * ThemeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ThemeTest extends TestCase
{
    public function testCheckReturnsFalseWhenNoBackendUser(): void
    {
        $trigger = new Theme('classic');

        self::assertFalse($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsTrueWhenThemeMatches(): void
    {
        $GLOBALS['BE_USER']->uc['theme'] = 'classic';

        $trigger = new Theme('classic');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsFalseWhenThemeDoesNotMatch(): void
    {
        $GLOBALS['BE_USER']->uc['theme'] = 'modern';

        $trigger = new Theme('classic');

        self::assertFalse($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsTrueWhenAnyOfTheGivenThemesMatches(): void
    {
        $GLOBALS['BE_USER']->uc['theme'] = 'modern';

        $trigger = new Theme('classic', 'modern');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckTreatsMissingUserSettingAsFresh(): void
    {
        unset($GLOBALS['BE_USER']->uc['theme']);

        $trigger = new Theme('fresh');

        self::assertTrue($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckDoesNotTreatMissingUserSettingAsClassic(): void
    {
        unset($GLOBALS['BE_USER']->uc['theme']);

        $trigger = new Theme('classic');

        self::assertFalse($trigger->check());
    }

    #[WithBackendUser]
    public function testCheckReturnsFalseForUnknownTheme(): void
    {
        $GLOBALS['BE_USER']->uc['theme'] = 'sepia';

        $trigger = new Theme('classic', 'modern', 'fresh');

        self::assertFalse($trigger->check());
    }
}
