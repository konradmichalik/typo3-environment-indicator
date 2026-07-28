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

namespace KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use TYPO3\CMS\Backend\Toolbar\ToolbarItemInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;

use function json_encode;
use function sprintf;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;

/**
 * PageTitleItem.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class PageTitleItem implements ToolbarItemInterface
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly PageRenderer $pageRenderer,
    ) {}

    public function checkAccess(): bool
    {
        return $this->isApplicable();
    }

    public function getItem(): string
    {
        $configuration = GeneralHelper::getIndicatorConfiguration()[PageTitle::class] ?? [];
        $prefix = GeneralHelper::replaceContextPlaceholder((string) ($configuration['prefix'] ?? ''));
        $suffix = GeneralHelper::replaceContextPlaceholder((string) ($configuration['suffix'] ?? ''));

        if ('' === $prefix && '' === $suffix) {
            return '';
        }

        $this->pageRenderer->addJsInlineCode(Configuration::EXT_KEY.'_pagetitle', $this->buildScript($prefix, $suffix), null, false, true);

        return '';
    }

    public function hasDropDown(): bool
    {
        return false;
    }

    public function getDropDown(): string
    {
        return '';
    }

    /**
     * @return array<string, string>
     */
    public function getAdditionalAttributes(): array
    {
        return [];
    }

    public function getIndex(): int
    {
        return 0;
    }

    /**
     * Prefixes/suffixes the document title client-side. A MutationObserver
     * re-applies it on backend SPA navigation (module switches keep the top
     * frame, so the observer survives).
     */
    private function buildScript(string $prefix, string $suffix): string
    {
        return sprintf(
            '(function(){var p=%s,s=%s;function a(){var t=document.title||"";'
            .'if(p&&t.indexOf(p)!==0){t=p+t;}'
            .'if(s&&!t.endsWith(s)){t=t+s;}'
            .'if(t!==document.title){document.title=t;}}a();'
            .'var e=document.querySelector("title");'
            .'if(e&&window.MutationObserver){new MutationObserver(a).observe(e,{childList:true});}})();',
            json_encode($prefix, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            json_encode($suffix, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        );
    }

    private function isApplicable(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['backend']['pageTitle'] ?? false)
            && GeneralHelper::isCurrentIndicator(PageTitle::class);
    }
}
