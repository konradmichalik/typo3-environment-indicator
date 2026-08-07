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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\HttpHeader;
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
        $this->configure(true, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        $response = $this->process();

        self::assertSame('Testing', $response->getHeaderLine('X-TYPO3-Environment'));
    }

    public function testCustomHeaderNameAndValueAreUsed(): void
    {
        $this->configure(true, ['name' => 'X-Environment', 'value' => 'Staging']);

        $response = $this->process();

        self::assertSame('Staging', $response->getHeaderLine('X-Environment'));
        self::assertFalse($response->hasHeader('X-TYPO3-Environment'));
    }

    public function testExistingHeaderIsNotOverwritten(): void
    {
        $this->configure(true, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        $response = $this->process(new HtmlResponse('<html lang="en"></html>', 200, ['X-TYPO3-Environment' => 'Production']));

        self::assertSame('Production', $response->getHeaderLine('X-TYPO3-Environment'));
    }

    public function testInvalidHeaderNameIsIgnored(): void
    {
        $this->configure(true, ['name' => 'Invalid Header Name', 'value' => '%context%']);

        self::assertFalse($this->process()->hasHeader('Invalid Header Name'));
    }

    public function testInvalidHeaderValueIsIgnored(): void
    {
        $this->configure(true, ['name' => 'X-TYPO3-Environment', 'value' => "Testing\r\nX-Injected: 1"]);

        $response = $this->process();

        self::assertFalse($response->hasHeader('X-TYPO3-Environment'));
        self::assertFalse($response->hasHeader('X-Injected'));
    }

    public function testEmptyValueIsIgnored(): void
    {
        $this->configure(true, ['name' => 'X-TYPO3-Environment', 'value' => '']);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    public function testDisabledFeatureToggleSkipsHeader(): void
    {
        $this->configure(false, ['name' => 'X-TYPO3-Environment', 'value' => '%context%']);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    public function testInactiveIndicatorSkipsHeader(): void
    {
        $this->configure(true, null);

        self::assertFalse($this->process()->hasHeader('X-TYPO3-Environment'));
    }

    /**
     * @param array<string, string>|null $indicatorConfiguration Null registers no indicator at all
     */
    private function configure(bool $featureEnabled, ?array $indicatorConfiguration): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['header' => $featureEnabled ? '1' : '0']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => null === $indicatorConfiguration ? [] : [HttpHeader::class => $indicatorConfiguration],
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
