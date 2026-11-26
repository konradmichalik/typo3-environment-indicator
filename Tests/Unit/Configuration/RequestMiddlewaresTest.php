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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * RequestMiddlewaresTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class RequestMiddlewaresTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function backendMiddlewareProvider(): iterable
    {
        yield 'favicon' => ['konradmichalik/typo3-environment-indicator/backend-favicon'];
        yield 'logo' => ['konradmichalik/typo3-environment-indicator/backend-logo'];
    }

    /**
     * The Admin, BackendUserGroup, Theme and ColorScheme triggers need
     * $GLOBALS['BE_USER'], and the indicator resolution is memoized on first use.
     */
    #[DataProvider('backendMiddlewareProvider')]
    public function testBackendMiddlewareRunsAfterAuthentication(string $identifier): void
    {
        $middlewares = require __DIR__.'/../../../Configuration/RequestMiddlewares.php';

        self::assertContains('typo3/cms-backend/authentication', $middlewares['backend'][$identifier]['after']);
    }
}
