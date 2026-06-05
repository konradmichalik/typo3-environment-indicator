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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Middleware;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendFaviconMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * FrontendFaviconMiddlewareTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class FrontendFaviconMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]);
    }

    public function testProcessSkipsWhenFeatureDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [
            'frontend' => ['favicon' => false],
        ];

        $middleware = new FrontendFaviconMiddleware(new ExtensionConfiguration());

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $result = $middleware->process($request, $handler);
        self::assertSame($response, $result);
    }

    public function testProcessSkipsWhenFeatureMissing(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [
            'frontend' => [],
        ];

        $middleware = new FrontendFaviconMiddleware(new ExtensionConfiguration());

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $result = $middleware->process($request, $handler);
        self::assertSame($response, $result);
    }

    public function testProcessSkipsWhenConfigMissing(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [];

        $middleware = new FrontendFaviconMiddleware(new ExtensionConfiguration());

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $result = $middleware->process($request, $handler);
        self::assertSame($response, $result);
    }

    public function testProcessReturnsResponseWhenFeatureEnabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [
            'frontend' => ['favicon' => true],
        ];

        $middleware = new FrontendFaviconMiddleware(new ExtensionConfiguration());

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())
            ->method('handle')
            ->willReturn($response);

        $result = $middleware->process($request, $handler);
        self::assertSame($response, $result);
    }
}
