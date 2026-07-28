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

use DOMDocument;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface, StreamFactoryInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

use function html_entity_decode;
use function libxml_clear_errors;
use function libxml_use_internal_errors;
use function preg_match_all;
use function str_contains;
use function str_ends_with;
use function str_starts_with;
use function strlen;
use function strtolower;
use function substr;
use function trim;

use const ENT_HTML5;
use const ENT_QUOTES;
use const LIBXML_NOERROR;
use const LIBXML_NOWARNING;
use const PREG_OFFSET_CAPTURE;

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
        $actualTitle = $this->resolveActualTitleText($body);
        if (null === $actualTitle) {
            return $response;
        }

        $modified = self::decorateMatchingTitleOccurrence($body, $actualTitle, $prefix, $suffix);
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

    /**
     * Resolves the actual document title text via an HTML parser rather than
     * a plain regex, so title-looking text inside e.g. an inline <script>
     * before the real <head><title> isn't mistaken for it.
     */
    private function resolveActualTitleText(string $body): ?string
    {
        $dom = new DOMDocument();

        $previousErrorSetting = libxml_use_internal_errors(true);
        try {
            // The XML encoding hint forces libxml to treat the body as UTF-8;
            // without it, loadHTML() defaults to ISO-8859-1 and mangles
            // multi-byte characters anywhere else in the returned DOM.
            $dom->loadHTML('<?xml encoding="UTF-8">'.$body, LIBXML_NOERROR | LIBXML_NOWARNING);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorSetting);
        }

        return $dom->getElementsByTagName('title')->item(0)?->textContent;
    }

    /**
     * Splices the decorated title text directly into the original, untouched
     * response body - only the exact <title> occurrence identified via
     * {@see resolveActualTitleText()} is affected, the rest of the document
     * stays byte-identical rather than being re-serialized from a DOM.
     */
    private static function decorateMatchingTitleOccurrence(string $body, string $actualTitle, string $prefix, string $suffix): ?string
    {
        $matchCount = preg_match_all('/<title\b[^>]*>(.*?)<\/title>/is', $body, $matches, PREG_OFFSET_CAPTURE);
        if (false === $matchCount || 0 === $matchCount) {
            return null;
        }

        $expectedText = trim($actualTitle);

        foreach ($matches[1] as [$innerHtml, $offset]) {
            if (trim(html_entity_decode($innerHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8')) !== $expectedText) {
                continue;
            }

            return substr($body, 0, $offset).self::decorate($innerHtml, $prefix, $suffix).substr($body, $offset + strlen($innerHtml));
        }

        return null;
    }

    private function isFeatureEnabled(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['frontend']['pageTitle'] ?? false);
    }
}
