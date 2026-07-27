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

namespace KonradMichalik\Typo3EnvironmentIndicator\EventListener\Backend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ColorUtility, GeneralHelper};
use TYPO3\CMS\Backend\LoginProvider\Event\ModifyPageLayoutOnLoginProviderSelectionEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\PageRenderer;

use function json_encode;
use function preg_match;
use function sprintf;
use function trim;

use const JSON_THROW_ON_ERROR;

/**
 * LoginBadgeListener.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final readonly class LoginBadgeListener
{
    public function __construct(
        private PageRenderer $pageRenderer,
    ) {}

    #[AsEventListener(identifier: 'typo3-environment-indicator/backend-login-badge')]
    public function __invoke(ModifyPageLayoutOnLoginProviderSelectionEvent $event): void
    {
        if (!GeneralHelper::isExtensionFeatureEnabled('backend/login')) {
            return;
        }

        if (!GeneralHelper::isCurrentIndicator(Login::class)) {
            return;
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[Login::class];

        $text = trim((string) ($configuration['text'] ?? Environment::getContext()->__toString()));
        if ('' === $text) {
            return;
        }

        $color = trim((string) ($configuration['color'] ?? ''));
        if (1 !== preg_match('/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color)) {
            $color = '#bd593a';
        }
        $description = trim((string) ($configuration['description'] ?? ''));
        $position = 'bottom' === ($configuration['position'] ?? 'top') ? 'bottom' : 'top';
        $textColor = ColorUtility::getOptimalTextColor($color, fallbackColor: '#ffffff');

        $this->pageRenderer->addCssInlineBlock(Configuration::EXT_KEY.'_login', $this->buildCss($color, $textColor, $position), null, false, true);
        $this->pageRenderer->addJsFooterInlineCode(Configuration::EXT_KEY.'_login', $this->buildScript($text, $description, $position), null, false, true);
    }

    private function buildCss(string $color, string $textColor, string $position): string
    {
        $radiusRule = 'top' === $position
            ? 'border-top-left-radius:inherit;border-top-right-radius:inherit;'
            : 'border-bottom-left-radius:inherit;border-bottom-right-radius:inherit;';

        return sprintf(
            '.typo3-environment-indicator-login{%s'
            .'background:%s;color:%s;text-align:center;padding:1rem;font-weight:bold;'
            .'font-family:Verdana,Arial,Helvetica,sans-serif;box-sizing:border-box;}'
            .'.typo3-environment-indicator-login small{display:block;font-weight:normal;opacity:.85;}'
            .'body>.typo3-environment-indicator-login{position:fixed;left:0;right:0;%s:0;z-index:9999;'
            .'border-radius:0;box-shadow:0 0 10px rgba(0,0,0,.3);}',
            $radiusRule,
            $color,
            $textColor,
            $position,
        );
    }

    /**
     * The badge text is injected via textContent, so it is XSS-safe regardless
     * of the configured value. Falls back to appending to the body if the
     * login card markup (.card-login) is not found.
     */
    private function buildScript(string $text, string $description, string $position): string
    {
        return sprintf(
            '(function(){var b=document.createElement("div");'
            .'b.className="typo3-environment-indicator-login";b.textContent=%s;'
            .'if(%s){var s=document.createElement("small");s.textContent=%s;b.appendChild(s);}'
            .'var c=document.querySelector(".card-login");'
            .'if(c){%s}else{(document.body||document.documentElement).appendChild(b);}'
            .'})();',
            json_encode($text, JSON_THROW_ON_ERROR),
            json_encode('' !== $description, JSON_THROW_ON_ERROR),
            json_encode($description, JSON_THROW_ON_ERROR),
            'top' === $position ? 'c.insertBefore(b,c.firstChild);' : 'c.appendChild(b);',
        );
    }
}
