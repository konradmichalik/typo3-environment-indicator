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
use KonradMichalik\Typo3EnvironmentIndicator\Image\BackendLogoHandler;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BackendLogoMiddleware.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BackendLogoMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->isFeatureEnabled()) {
            return $handler->handle($request);
        }
        $this->processLogo();

        return $handler->handle($request);
    }

    private function isFeatureEnabled(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['backend']['logo'] ?? false);
    }

    private function processLogo(): void
    {
        $currentLogo = $this->getCurrentLogo();
        $logoHandler = GeneralUtility::makeInstance(BackendLogoHandler::class);
        $newLogo = $logoHandler->process($currentLogo);

        $this->setBackendLogo($newLogo);
    }

    private function getCurrentLogo(): string
    {
        $backendLogo = $this->extensionConfiguration->get('backend', 'backendLogo');

        if (null !== $backendLogo && '' !== $backendLogo) {
            return $backendLogo;
        }

        return 'EXT:backend/Resources/Public/Images/typo3_logo_orange.svg';
    }

    private function setBackendLogo(string $logoPath): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['backendLogo'] = $logoPath;
    }
}
