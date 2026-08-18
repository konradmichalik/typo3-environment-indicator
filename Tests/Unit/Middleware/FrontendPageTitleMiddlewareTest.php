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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\FrontendPageTitleMiddleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\{HtmlResponse, JsonResponse, StreamFactory};

use function strlen;

/**
 * FrontendPageTitleMiddlewareTest.
 *
 * "current" is deliberately not part of the class-level default: ttt
 * deep-merges method-level overrides into the class baseline rather than
 * replacing it, so a shared default here would merge with (not replace)
 * each test's own PageTitle config, e.g. leaving both "prefix" and
 * "suffix" set at once.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['pageTitle' => true]]], 'EXTCONF' => [Configuration::EXT_KEY => ['resolved' => true]]])]
final class FrontendPageTitleMiddlewareTest extends TestCase
{
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testTitleIsPrefixed(): void
    {
        $response = $this->process(new HtmlResponse('<html><head><title>Home</title></head><body></body></html>'));

        self::assertStringContainsString('<title>[STG] Home</title>', (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['suffix' => ' (STG)'],
    ]]]])]
    public function testTitleIsSuffixed(): void
    {
        $response = $this->process(new HtmlResponse('<html><head><title>Home</title></head></html>'));

        self::assertStringContainsString('<title>Home (STG)</title>', (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testTitleIsNotPrefixedTwice(): void
    {
        $response = $this->process(new HtmlResponse('<html><head><title>[STG] Home</title></head></html>'));

        self::assertStringContainsString('<title>[STG] Home</title>', (string) $response->getBody());
        self::assertStringNotContainsString('[STG] [STG]', (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testTitleLikeTextInsideInlineScriptIsIgnored(): void
    {
        $html = '<html><head><script>var x="<title>Preview</title>";</script><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertStringContainsString('<title>[STG] Home</title>', (string) $response->getBody());
        self::assertStringContainsString('<script>var x="<title>Preview</title>";</script>', (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testNonHtmlResponseIsUntouched(): void
    {
        $original = new JsonResponse(['title' => 'Home']);
        $body = (string) $original->getBody();

        $response = $this->process($original);

        self::assertSame($body, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['pageTitle' => false]]]])]
    public function testResponseIsUntouchedWhenFeatureDisabled(): void
    {
        $html = '<html><head><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
    public function testResponseIsUntouchedWhenIndicatorNotResolved(): void
    {
        $html = '<html><head><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => [],
    ]]]])]
    public function testResponseIsUntouchedWhenNoPrefixOrSuffixConfigured(): void
    {
        $html = '<html><head><title>Home</title></head></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testResponseIsUntouchedWhenNoTitleTagPresent(): void
    {
        $html = '<html><head></head><body>No title here</body></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testResponseIsUntouchedWhenTitleTagHasNoClosingTag(): void
    {
        // DOMDocument's lenient HTML5 parser still resolves the title text
        // ("Home") without a closing tag, but the exact-match regex used to
        // splice the decorated title back into the byte-identical body
        // requires a literal "</title>" and finds none.
        $html = '<html><head><title>Home</head><body>Text</body></html>';

        $response = $this->process(new HtmlResponse($html));

        self::assertSame($html, (string) $response->getBody());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [
        PageTitle::class => ['prefix' => '[STG] '],
    ]]]])]
    public function testContentLengthHeaderIsUpdatedToMatchDecoratedBodyLength(): void
    {
        $original = (new HtmlResponse('<html><head><title>Home</title></head></html>'))
            ->withHeader('Content-Length', '1');

        $response = $this->process($original);

        self::assertSame((string) strlen((string) $response->getBody()), $response->getHeaderLine('Content-Length'));
    }

    public function testDecorateMatchingTitleOccurrenceReturnsNullWhenNoOccurrenceMatchesActualTitle(): void
    {
        $method = new ReflectionMethod(FrontendPageTitleMiddleware::class, 'decorateMatchingTitleOccurrence');

        $result = $method->invoke(null, '<html><head><title>Other</title></head></html>', 'Home', '[STG] ', '');

        self::assertNull($result);
    }

    private function process(ResponseInterface $response): ResponseInterface
    {
        $middleware = new FrontendPageTitleMiddleware(new ExtensionConfiguration(), new StreamFactory());

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response);

        return $middleware->process($this->createStub(ServerRequestInterface::class), $handler);
    }
}
