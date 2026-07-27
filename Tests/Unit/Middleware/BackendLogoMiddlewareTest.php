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
use KonradMichalik\Typo3EnvironmentIndicator\Image\BackendLogoHandler;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\BackendLogoMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BackendLogoMiddlewareTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class BackendLogoMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['logo' => false]]]])]
    public function testProcessSkipsWhenFeatureDisabled(): void
    {
        $middleware = new BackendLogoMiddleware(new ExtensionConfiguration());

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

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => null]]])]
    public function testProcessSkipsWhenFeatureMissing(): void
    {
        $middleware = new BackendLogoMiddleware(new ExtensionConfiguration());

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

    public function testProcessCallsLogoHandlerWhenFeatureEnabled(): void
    {
        $extConfigMock = $this->createMock(ExtensionConfiguration::class);
        $extConfigMock->method('get')
            ->willReturnCallback(static function (string $ext, string $path = '') {
                if (Configuration::EXT_KEY === $ext) {
                    return ['backend' => ['logo' => true]];
                }

                return null;
            });

        $logoHandlerMock = $this->createMock(BackendLogoHandler::class);
        $logoHandlerMock->method('process')->willReturn('/processed/logo.svg');
        GeneralUtility::addInstance(BackendLogoHandler::class, $logoHandlerMock);

        $middleware = new BackendLogoMiddleware($extConfigMock);

        $request = $this->createStub(ServerRequestInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $response = $this->createStub(ResponseInterface::class);

        $handler->expects(self::once())->method('handle')->willReturn($response);

        $result = $middleware->process($request, $handler);

        self::assertSame($response, $result);
        self::assertSame('/processed/logo.svg', $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendLogo']);
    }
}
