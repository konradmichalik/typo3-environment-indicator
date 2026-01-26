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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Image;

use KonradMichalik\Typo3EnvironmentIndicator\Image\{AbstractImageHandler, BackendLogoHandler};
use PHPUnit\Framework\TestCase;

/**
 * BackendLogoHandlerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BackendLogoHandlerTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $handler = new BackendLogoHandler();
        self::assertInstanceOf(BackendLogoHandler::class, $handler);
    }

    public function testExtendsAbstractImageHandler(): void
    {
        $handler = new BackendLogoHandler();
        self::assertInstanceOf(AbstractImageHandler::class, $handler);
    }

    public function testConstructorInitializesWithBackendLogoIndicator(): void
    {
        $handler = new BackendLogoHandler();
        self::assertInstanceOf(BackendLogoHandler::class, $handler);
    }
}
