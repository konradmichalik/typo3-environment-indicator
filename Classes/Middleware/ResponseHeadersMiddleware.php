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

namespace KonradMichalik\Typo3EnvironmentIndicator\Middleware;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\{HttpHeader, Robots};
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Psr\Log\{LoggerInterface, NullLogger};

use function preg_match;
use function trim;

/**
 * ResponseHeadersMiddleware.
 *
 * Adds environment indicator headers to frontend responses. Deliberately
 * limited to headers - the response body is never touched here.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ResponseHeadersMiddleware implements MiddlewareInterface
{
    /**
     * RFC 9110 field name (token) and field value, so a misconfigured header
     * cannot make withHeader() throw in the middle of the frontend stack.
     */
    private const HEADER_NAME_PATTERN = '/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/';
    private const HEADER_VALUE_PATTERN = '/^[\x20\x09\x21-\x7E\x80-\xFE]*$/';

    /**
     * Fixed by the crawler protocol - unlike the HttpHeader indicator this
     * name is not configurable.
     */
    private const ROBOTS_HEADER_NAME = 'X-Robots-Tag';

    public function __construct(
        protected readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        foreach ($this->collectHeaders() as $name => $value) {
            // Never clobber a header the application already set on purpose.
            if ($response->hasHeader($name)) {
                continue;
            }

            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function collectHeaders(): array
    {
        return [
            ...$this->resolveEnvironmentHeader(),
            ...$this->resolveRobotsHeader(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function resolveEnvironmentHeader(): array
    {
        if (!GeneralHelper::isExtensionFeatureEnabled('frontend/header')) {
            return [];
        }

        if (!GeneralHelper::isCurrentIndicator(HttpHeader::class)) {
            return [];
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[HttpHeader::class];
        $name = trim((string) ($configuration['name'] ?? ''));
        $value = trim(GeneralHelper::replaceContextPlaceholder((string) ($configuration['value'] ?? '')));

        if ('' === $value) {
            return [];
        }

        if (1 !== preg_match(self::HEADER_NAME_PATTERN, $name)) {
            $this->logger->warning('Ignoring HTTP header with invalid name "{name}" from the HttpHeader indicator configuration.', ['name' => $name]);

            return [];
        }

        if (1 !== preg_match(self::HEADER_VALUE_PATTERN, $value)) {
            $this->logger->warning('Ignoring HTTP header "{name}" with invalid value "{value}" from the HttpHeader indicator configuration.', ['name' => $name, 'value' => $value]);

            return [];
        }

        return [$name => $value];
    }

    /**
     * @return array<string, string>
     */
    private function resolveRobotsHeader(): array
    {
        if (!GeneralHelper::isExtensionFeatureEnabled('frontend/robots')) {
            return [];
        }

        if (!GeneralHelper::isCurrentIndicator(Robots::class)) {
            return [];
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[Robots::class];
        $content = trim((string) ($configuration['content'] ?? ''));

        if ('' === $content) {
            return [];
        }

        if (1 !== preg_match(self::HEADER_VALUE_PATTERN, $content)) {
            $this->logger->warning('Ignoring invalid X-Robots-Tag content from the Robots indicator configuration.');

            return [];
        }

        return [self::ROBOTS_HEADER_NAME => $content];
    }
}
