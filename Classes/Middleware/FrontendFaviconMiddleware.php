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
use KonradMichalik\Typo3EnvironmentIndicator\Image\FaviconHandler;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_key_exists;

/**
 * FrontendFaviconMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrontendFaviconMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isFeatureEnabled()) {
            return $handler->handle($request);
        }

        $this->processFavicon($request);

        return $handler->handle($request);
    }

    private function isFeatureEnabled(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['frontend']['favicon'] ?? false);
    }

    private function processFavicon(ServerRequestInterface $request): void
    {
        $typoScript = $request->getAttribute('frontend.typoscript');

        if (null === $typoScript
            || !$typoScript->hasPage()
            || !array_key_exists('shortcutIcon', $typoScript->getPageArray())
            || '' === $typoScript->getPageArray()['shortcutIcon']
        ) {
            return;
        }

        $typoScriptPageArray = $typoScript->getPageArray();
        $currentFavicon = $typoScriptPageArray['shortcutIcon'];

        $faviconHandler = GeneralUtility::makeInstance(FaviconHandler::class);
        $newFavicon = $faviconHandler->process($currentFavicon);

        $typoScriptPageArray['shortcutIcon'] = $newFavicon;
        $typoScript->setPageArray($typoScriptPageArray);
    }
}
