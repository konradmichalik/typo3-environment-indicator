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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendPageTitleMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\{HtmlResponse, JsonResponse, StreamFactory};

/**
 * FrontendPageTitleMiddlewareTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class FrontendPageTitleMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = ['frontend' => ['pageTitle' => true]];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            PageTitle::class => ['prefix' => '[STG] '],
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
        );
    }

    public function testTitleIsPrefixed(): void
    {
        $response = $this->process(new HtmlResponse('<html><head><title>Home</title></head><body></body></html>'));

        self::assertStringContainsString('<title>[STG] Home</title>', (string) $response->getBody());
    }

    public function testTitleIsSuffixed(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            PageTitle::class => ['suffix' => ' (STG)'],
        ];

        $response = $this->process(new HtmlResponse('<html><head><title>Home</title></head></html>'));

        self::assertStringContainsString('<title>Home (STG)</title>', (string) $response->getBody());
    }

    public function testTitleIsNotPrefixedTwice(): void
    {
        $response = $this->process(new HtmlResponse('<html><head><title>[STG] Home</title></head></html>'));

        self::assertStringContainsString('<title>[STG] Home</title>', (string) $response->getBody());
        self::assertStringNotContainsString('[STG] [STG]', (string) $response->getBody());
    }

    public function testNonHtmlResponseIsUntouched(): void
    {
        $original = new JsonResponse(['title' => 'Home']);
        $body = (string) $original->getBody();

        $response = $this->process($original);

        self::assertSame($body, (string) $response->getBody());
    }

    public function testResponseIsUntouchedWhenFeatureDisabled(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = ['frontend' => ['pageTitle' => false]];
        $html = '<html><head><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    public function testResponseIsUntouchedWhenIndicatorNotResolved(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
        $html = '<html><head><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    private function process(ResponseInterface $response): ResponseInterface
    {
        $middleware = new FrontendPageTitleMiddleware(new ExtensionConfiguration(), new StreamFactory());

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        return $middleware->process($this->createStub(ServerRequestInterface::class), $handler);
    }
}
