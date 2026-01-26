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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Enum;

use KonradMichalik\Typo3EnvironmentIndicator\Enum\Scope;
use PHPUnit\Framework\TestCase;

/**
 * ScopeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ScopeTest extends TestCase
{
    public function testScopeValues(): void
    {
        $scopes = Scope::cases();

        self::assertCount(3, $scopes);
        self::assertEquals(Scope::Frontend, $scopes[0]);
        self::assertEquals(Scope::Backend, $scopes[1]);
        self::assertEquals(Scope::Global, $scopes[2]);
    }

    public function testScopeNames(): void
    {
        self::assertEquals('Frontend', Scope::Frontend->name);
        self::assertEquals('Backend', Scope::Backend->name);
        self::assertEquals('Global', Scope::Global->name);
    }
}
