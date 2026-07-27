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

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Image\FaviconHandler;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\BackendFaviconMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BackendFaviconMiddlewareTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class BackendFaviconMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['favicon' => false]]]])]
    public function testProcessSkipsWhenFeatureDisabled(): void
    {
        $middleware = new BackendFaviconMiddleware(new ExtensionConfiguration());

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

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => []]]])]
    public function testProcessSkipsWhenFeatureMissing(): void
    {
        $middleware = new BackendFaviconMiddleware(new ExtensionConfiguration());

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

    public function testProcessCallsFaviconHandlerWhenFeatureEnabled(): void
    {
        $extConfigMock = $this->createMock(ExtensionConfiguration::class);
        $extConfigMock->method('get')
            ->willReturnCallback(static function (string $ext, string $path = '') {
                if (Configuration::EXT_KEY === $ext) {
                    return ['backend' => ['favicon' => true]];
                }

                return null;
            });

        $faviconHandlerMock = $this->createMock(FaviconHandler::class);
        $faviconHandlerMock->method('process')->willReturn('/processed/favicon.ico');
        GeneralUtility::addInstance(FaviconHandler::class, $faviconHandlerMock);

        $middleware = new BackendFaviconMiddleware($extConfigMock);

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())->method('handle')->willReturn($response);

        $result = $middleware->process($request, $handler);

        self::assertSame($response, $result);
        self::assertSame('/processed/favicon.ico', $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendFavicon']);
    }
}
