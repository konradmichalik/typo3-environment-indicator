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

use function trim;

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

    /**
     * An empty text switches the badge off: the template renders nothing and
     * the script is never registered.
     */
    #[AsAllowedCallable]
    public function getText(): string
    {
        // The TypoScript condition already gates the template on an active
        // indicator; this keeps a stray call from emitting a badge anyway.
        $configuration = $this->getConfiguration();
        if (null === $configuration) {
            return '';
        }

        return trim(GeneralHelper::replaceContextPlaceholder((string) ($configuration['text'] ?? '%context%')));
    }

    #[AsAllowedCallable]
    public function getColor(): string
    {
        return (string) (($this->getConfiguration()['color'] ?? null) ?? self::FALLBACK_COLOR);
    }

    #[AsAllowedCallable]
    public function getTextColor(): string
    {
        return ColorUtility::getOptimalTextColor($this->getColor());
    }

    /**
     * @return array<string|int, mixed>|null Null when the indicator is not active
     */
    private function getConfiguration(): ?array
    {
        return GeneralHelper::getIndicatorConfiguration()[Console::class] ?? null;
    }
}
