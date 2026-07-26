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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface, StreamFactoryInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

use function preg_replace_callback;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function strlen;
use function strtolower;

/**
 * FrontendPageTitleMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrontendPageTitleMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected readonly ExtensionConfiguration $extensionConfiguration,
        protected readonly StreamFactoryInterface $streamFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (!$this->isFeatureEnabled() || !GeneralHelper::isCurrentIndicator(PageTitle::class)) {
            return $response;
        }

        if (!str_contains(strtolower($response->getHeaderLine('Content-Type')), 'text/html')) {
            return $response;
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[PageTitle::class];
        $prefix = GeneralHelper::replaceContextPlaceholder((string) ($configuration['prefix'] ?? ''));
        $suffix = GeneralHelper::replaceContextPlaceholder((string) ($configuration['suffix'] ?? ''));

        if ('' === $prefix && '' === $suffix) {
            return $response;
        }

        $body = (string) $response->getBody();
        $modified = preg_replace_callback(
            '/(<title\b[^>]*>)(.*?)(<\/title>)/is',
            static fn (array $matches): string => $matches[1].self::decorate($matches[2], $prefix, $suffix).$matches[3],
            $body,
            1,
        );

        if (null === $modified || $modified === $body) {
            return $response;
        }

        $response = $response->withBody($this->streamFactory->createStream($modified));

        if ($response->hasHeader('Content-Length')) {
            $response = $response->withHeader('Content-Length', (string) strlen($modified));
        }

        return $response;
    }

    private static function decorate(string $title, string $prefix, string $suffix): string
    {
        // Idempotency: never decorate twice (e.g. a shared full-page cache).
        if ('' !== $prefix && !str_starts_with($title, $prefix)) {
            $title = $prefix.$title;
        }

        if ('' !== $suffix && !str_ends_with($title, $suffix)) {
            $title .= $suffix;
        }

        return $title;
    }

    private function isFeatureEnabled(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['frontend']['pageTitle'] ?? false);
    }
}
