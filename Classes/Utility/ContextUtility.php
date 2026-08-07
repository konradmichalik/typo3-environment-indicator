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
use function in_array;
use function is_string;
use function preg_replace;
use function str_replace;
use function strtolower;
use function trim;

/**
 * ContextUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextUtility
{
    private const POSITIONS = ['top left', 'top right', 'bottom left', 'bottom right'];
    private const DEFAULT_POSITION = 'top left';

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

    /**
     * The four corners are the complete state model of the hint's position,
     * so they are expressed as a single modifier class instead of separate
     * offset strings that would have to be parsed again client-side.
     */
    #[AsAllowedCallable]
    public function getPositionClass(): string
    {
        $position = (string) preg_replace('/\s+/', ' ', strtolower(trim((string) ($this->getFrontendHintConfiguration()['position'] ?? ''))));

        if (!in_array($position, self::POSITIONS, true)) {
            $position = self::DEFAULT_POSITION;
        }

        return 'technical-context--'.str_replace(' ', '-', $position);
    }

    #[AsAllowedCallable]
    public function getDescription(): string
    {
        return trim((string) ($this->getFrontendHintConfiguration()['description'] ?? ''));
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
