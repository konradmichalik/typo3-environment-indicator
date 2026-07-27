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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Middleware;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendPageTitleMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\{HtmlResponse, StreamFactory};
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * FrontendPageTitleMiddlewareTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrontendPageTitleMiddlewareTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['pageTitle' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [PageTitle::class => ['prefix' => '[%context%] ']],
                'resolved' => true,
            ]],
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testTitleIsPrefixedWithApplicationContext(): void
    {
        $middleware = new FrontendPageTitleMiddleware(
            $this->get(ExtensionConfiguration::class),
            new StreamFactory(),
        );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(
            new HtmlResponse('<html><head><title>Home</title></head></html>'),
        );

        $response = $middleware->process($this->createStub(ServerRequestInterface::class), $handler);

        self::assertStringContainsString('<title>[Testing] Home</title>', (string) $response->getBody());
    }
}
