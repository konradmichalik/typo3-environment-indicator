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

use KonradMichalik\Ttt\Attribute\Typo3ConfVarsSentinel;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\{HttpHeader, Robots};
use KonradMichalik\Typo3EnvironmentIndicator\Middleware\ResponseHeadersMiddleware;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ResponseHeadersMiddlewareTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ResponseHeadersMiddlewareTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testHeaderIsAddedWithResolvedApplicationContext(): void
    {
        $this->configureHttpHeader(true, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        $response = $this->process();

        self::assertSame('Testing', $response->getHeaderLine('X-TYPO3-Environment'));
    }

    public function testCustomHeaderNameAndValueAreUsed(): void
    {
        $this->configureHttpHeader(true, ['name' => 'X-Environment', 'value' => 'Staging']);

        $response = $this->process();

        self::assertSame('Staging', $response->getHeaderLine('X-Environment'));
        self::assertFalse($response->hasHeader('X-TYPO3-Environment'));
    }

    public function testExistingHeaderIsNotOverwritten(): void
    {
        $this->configureHttpHeader(true, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        $response = $this->process(new HtmlResponse('<html lang="en"></html>', 200, ['X-TYPO3-Environment' => 'Production']));

        self::assertSame('Production', $response->getHeaderLine('X-TYPO3-Environment'));
    }

    public function testInvalidHeaderNameIsIgnored(): void
    {
        $this->configureHttpHeader(true, ['name' => 'Invalid Header Name', 'value' => '%context%']);

        self::assertFalse($this->process()->hasHeader('Invalid Header Name'));
    }

    public function testInvalidHeaderValueIsIgnored(): void
    {
        $this->configureHttpHeader(true, ['name' => 'X-TYPO3-Environment', 'value' => "Testing\r\nX-Injected: 1"]);

        $response = $this->process();

        self::assertFalse($response->hasHeader('X-TYPO3-Environment'));
        self::assertFalse($response->hasHeader('X-Injected'));
    }

    public function testEmptyValueIsIgnored(): void
    {
        $this->configureHttpHeader(true, ['name' => 'X-TYPO3-Environment', 'value' => '']);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    public function testDisabledFeatureToggleSkipsHeader(): void
    {
        $this->configureHttpHeader(false, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    public function testInactiveIndicatorSkipsHeader(): void
    {
        $this->configure(['header' => '1'], []);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    public function testRobotsHeaderIsAdded(): void
    {
        $this->configure(['robots' => '1'], [Robots::class => ['content' => 'noindex, nofollow']]);

        self::assertSame('noindex, nofollow', $this->process()->getHeaderLine('X-Robots-Tag'));
    }

    public function testRobotsHeaderUsesConfiguredContent(): void
    {
        $this->configure(['robots' => '1'], [Robots::class => ['content' => 'noindex']]);

        self::assertSame('noindex', $this->process()->getHeaderLine('X-Robots-Tag'));
    }

    public function testExistingRobotsHeaderIsNotOverwritten(): void
    {
        $this->configure(['robots' => '1'], [Robots::class => ['content' => 'noindex, nofollow']]);

        $response = $this->process(new HtmlResponse('<html lang="en"></html>', 200, ['X-Robots-Tag' => 'all']));

        self::assertSame('all', $response->getHeaderLine('X-Robots-Tag'));
    }

    public function testInvalidRobotsContentIsIgnored(): void
    {
        $this->configure(['robots' => '1'], [Robots::class => ['content' => "noindex\r\nX-Injected: 1"]]);

        $response = $this->process();

        self::assertFalse($response->hasHeader('X-Robots-Tag'));
        self::assertFalse($response->hasHeader('X-Injected'));
    }

    public function testEmptyRobotsContentIsIgnored(): void
    {
        $this->configure(['robots' => '1'], [Robots::class => ['content' => '']]);

        self::assertFalse($this->process()->hasHeader('X-Robots-Tag'));
    }

    public function testDisabledRobotsToggleSkipsHeader(): void
    {
        $this->configure(['robots' => '0'], [Robots::class => ['content' => 'noindex, nofollow']]);

        self::assertFalse($this->process()->hasHeader('X-Robots-Tag'));
    }

    public function testInactiveRobotsIndicatorSkipsHeader(): void
    {
        $this->configure(['robots' => '1'], []);

        self::assertFalse($this->process()->hasHeader('X-Robots-Tag'));
    }

    public function testBothIndicatorsContributeIndependently(): void
    {
        $this->configure(
            ['header' => '1', 'robots' => '1'],
            [
                HttpHeader::class => ['name' => 'X-TYPO3-Environment', 'value' => '%context%'],
                Robots::class => ['content' => 'noindex, nofollow'],
            ],
        );

        $response = $this->process();

        self::assertSame('Testing', $response->getHeaderLine('X-TYPO3-Environment'));
        self::assertSame('noindex, nofollow', $response->getHeaderLine('X-Robots-Tag'));
    }

    public function testRobotsHeaderIsUnaffectedByTheHttpHeaderToggle(): void
    {
        $this->configure(
            ['header' => '0', 'robots' => '1'],
            [
                HttpHeader::class => ['name' => 'X-TYPO3-Environment', 'value' => '%context%'],
                Robots::class => ['content' => 'noindex, nofollow'],
            ],
        );

        $response = $this->process();

        self::assertFalse($response->hasHeader('X-TYPO3-Environment'));
        self::assertSame('noindex, nofollow', $response->getHeaderLine('X-Robots-Tag'));
    }

    /**
     * @param array<string, string> $indicatorConfiguration
     */
    private function configureHttpHeader(bool $featureEnabled, array $indicatorConfiguration): void
    {
        $this->configure(
            ['header' => $featureEnabled ? '1' : '0'],
            [HttpHeader::class => $indicatorConfiguration],
        );
    }

    /**
     * @param array<string, string>                      $features   Frontend feature toggles, e.g. ['header' => '1']
     * @param array<class-string, array<string, string>> $indicators Resolved indicator map; an empty map registers no indicator at all
     */
    private function configure(array $features, array $indicators): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => $features]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                // The sandbox deep-merges, so an override of [] would be a no-op
                // against an already populated subtree. The sentinel clears the
                // indicator map regardless of what the bootstrap left behind.
                'current' => [] === $indicators ? Typo3ConfVarsSentinel::Unset : $indicators,
                'resolved' => true,
            ]],
        ]);
    }

    private function process(?ResponseInterface $response = null): ResponseInterface
    {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($response ?? new HtmlResponse('<html lang="en"></html>', 200, []));

        return (new ResponseHeadersMiddleware())->process($this->createStub(ServerRequestInterface::class), $handler);
    }
}
