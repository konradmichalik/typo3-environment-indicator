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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\Console;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;

use function sprintf;
use function str_replace;
use function trim;

/**
 * ConsoleUtility.
 *
 * Single source of truth for how the badge looks and how its text has to be
 * escaped, so the frontend and backend badges cannot drift apart.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConsoleUtility
{
    /**
     * Neutral fallback so an unconfigured badge is readable without implying a
     * context it was never told about - the presets set the real color.
     */
    private const FALLBACK_COLOR = '#767676';

    private const BADGE_STYLE = 'background:%s;color:%s;padding:2px 6px;border-radius:3px';

    /**
     * The console argument, ready to be passed as-is: the "%c" directive that
     * applies the style, followed by the text. A percent sign in the text is
     * doubled, because the console would otherwise read it as another format
     * directive and swallow the styling of everything after it.
     *
     * Returns an empty string when the badge is switched off, which is what
     * both callers use to decide whether to emit anything at all.
     */
    #[AsAllowedCallable]
    public function getBadgeText(): string
    {
        // The TypoScript condition and the toolbar item already gate on an
        // active indicator; this keeps a stray call from emitting a badge.
        $configuration = $this->getConfiguration();
        if (null === $configuration) {
            return '';
        }

        $text = trim(GeneralHelper::replaceContextPlaceholder((string) ($configuration['text'] ?? '%context%')));
        if ('' === $text) {
            return '';
        }

        return '%c'.str_replace('%', '%%', $text);
    }

    #[AsAllowedCallable]
    public function getStyle(): string
    {
        // Callable from arbitrary TypoScript, so it must not depend on the
        // caller having checked that the indicator resolved at all.
        $configuration = $this->getConfiguration() ?? [];
        $color = (string) ($configuration['color'] ?? self::FALLBACK_COLOR);

        return sprintf(self::BADGE_STYLE, $color, ColorUtility::getOptimalTextColor($color));
    }

    /**
     * @return array<string|int, mixed>|null Null when the indicator is not active
     */
    private function getConfiguration(): ?array
    {
        return GeneralHelper::getIndicatorConfiguration()[Console::class] ?? null;
    }
}
