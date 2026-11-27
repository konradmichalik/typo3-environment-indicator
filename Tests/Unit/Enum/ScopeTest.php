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
        self::assertSame(Scope::Frontend, $scopes[0]);
        self::assertSame(Scope::Backend, $scopes[1]);
        self::assertSame(Scope::Global, $scopes[2]);
    }

    public function testScopeNames(): void
    {
        self::assertSame('Frontend', Scope::Frontend->name);
        self::assertSame('Backend', Scope::Backend->name);
        self::assertSame('Global', Scope::Global->name);
    }
}
