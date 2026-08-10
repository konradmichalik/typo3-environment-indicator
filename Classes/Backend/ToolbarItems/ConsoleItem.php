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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\Console;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\{ConsoleUtility, GeneralHelper};
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
 * ConsoleItem.
 *
 * Renders no toolbar entry of its own - it only exists because toolbar items
 * are executed during backend rendering, which is where PageRenderer works
 * reliably. The same call from a middleware would have no effect.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConsoleItem implements ToolbarItemInterface
{
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly PageRenderer $pageRenderer,
        private readonly ConsoleUtility $consoleUtility,
    ) {}

    public function checkAccess(): bool
    {
        return $this->isApplicable();
    }

    public function getItem(): string
    {
        $badgeText = $this->consoleUtility->getBadgeText();
        if ('' === $badgeText) {
            return '';
        }

        // Routes through PageRenderer, which applies the backend CSP nonce
        // itself. A hand-written nonce attribute would be dropped and the
        // script blocked - the backend enforces a nonce-based policy by
        // default, unlike the frontend.
        GeneralHelper::addNonceGuardedJsInlineCode(
            $this->pageRenderer,
            Configuration::EXT_KEY.'_console',
            $this->buildScript($badgeText, $this->consoleUtility->getStyle()),
        );

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

    private function buildScript(string $badgeText, string $style): string
    {
        return sprintf('console.info(%s,%s);', self::encode($badgeText), self::encode($style));
    }

    private static function encode(string $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    private function isApplicable(): bool
    {
        $extensionConfig = $this->extensionConfiguration->get(Configuration::EXT_KEY);

        return true === (bool) ($extensionConfig['backend']['console'] ?? false)
            && GeneralHelper::isCurrentIndicator(Console::class);
    }
}
