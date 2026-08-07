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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Console;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;

use function json_encode;
use function sprintf;
use function str_replace;
use function trim;

use const JSON_HEX_AMP;
use const JSON_HEX_APOS;
use const JSON_HEX_QUOT;
use const JSON_HEX_TAG;
use const JSON_THROW_ON_ERROR;

/**
 * ConsoleUtility.
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
     * Builds the complete console statement. Both arguments are JSON-encoded
     * with the HTML-hex flags, so the result carries no characters that could
     * break out of the inline script it is embedded in.
     */
    #[AsAllowedCallable]
    public function getScript(): string
    {
        // The TypoScript condition already gates the template on an active
        // indicator; this keeps a stray call from emitting a badge anyway.
        $configuration = GeneralHelper::getIndicatorConfiguration()[Console::class] ?? null;
        if (null === $configuration) {
            return '';
        }

        $text = trim(GeneralHelper::replaceContextPlaceholder((string) ($configuration['text'] ?? '%context%')));
        if ('' === $text) {
            return '';
        }

        $color = (string) ($configuration['color'] ?? self::FALLBACK_COLOR);

        return sprintf(
            'console.info(%s,%s);',
            self::encode('%c'.self::escapeFormatDirectives($text)),
            self::encode(sprintf(self::BADGE_STYLE, $color, ColorUtility::getOptimalTextColor($color))),
        );
    }

    /**
     * A percent sign in the badge text would be read as a console format
     * directive and swallow the styling of everything after it.
     */
    private static function escapeFormatDirectives(string $text): string
    {
        return str_replace('%', '%%', $text);
    }

    private static function encode(string $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}
