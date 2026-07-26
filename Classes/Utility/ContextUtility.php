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

namespace KonradMichalik\Typo3EnvironmentIndicator\Utility;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Hint;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\SiteFinder;

use function array_key_exists;
use function is_string;

/**
 * ContextUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextUtility
{
    public function __construct(private readonly ?SiteFinder $siteFinder = null) {}

    #[AsAllowedCallable]
    public function getContext(): string
    {
        return Environment::getContext()->__toString();
    }

    #[AsAllowedCallable]
    public function getColor(): string
    {
        return $this->getFrontendHintConfiguration()['color'] ?? 'transparent';
    }

    #[AsAllowedCallable]
    public function getTextColor(): string
    {
        return ColorUtility::getOptimalTextColor($this->getFrontendHintConfiguration()['color'] ?? 'transparent');
    }

    #[AsAllowedCallable]
    public function getPositionX(): string
    {
        return explode(' ', $this->getFrontendHintConfiguration()['position'] ?? 'left top')[0].':0';
    }

    #[AsAllowedCallable]
    public function getPositionY(): string
    {
        return explode(' ', $this->getFrontendHintConfiguration()['position'] ?? 'left top')[1].':0';
    }

    #[AsAllowedCallable]
    public function getDescription(): string
    {
        return (string) ($this->getFrontendHintConfiguration()['description'] ?? '');
    }

    #[AsAllowedCallable]
    public function getTitle(): string
    {
        $title = $this->getFrontendHintConfiguration()['text'] ?? null;
        if (null !== $title) {
            return $title;
        }

        $request = $this->getRequest();
        if (null === $request) {
            return '';
        }

        $routing = $request->getAttribute('routing');
        if (!$routing instanceof PageArguments) {
            return '';
        }

        if (null === $this->siteFinder) {
            return '';
        }

        $pid = $routing->getPageId();

        try {
            $site = $this->siteFinder->getSiteByPageId($pid);
        } catch (SiteNotFoundException) {
            return '';
        }

        $configuration = $site->getConfiguration();

        return array_key_exists('websiteTitle', $configuration) && is_string($configuration['websiteTitle'])
            ? $configuration['websiteTitle']
            : $site->getIdentifier();
    }

    protected function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }

    /**
     * @return array<string|int, mixed>
     */
    private function getFrontendHintConfiguration(): array
    {
        return GeneralHelper::getIndicatorConfiguration()[Hint::class] ?? [];
    }
}
