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

use Exception;
use Intervention\Image\Interfaces\ImageManagerInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function array_key_exists;
use function str_contains;
use function str_replace;

/**
 * GeneralHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class GeneralHelper
{
    public static function getFolder(IndicatorInterface $indicator, bool $publicPath = true): string
    {
        $defaultPath = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'][$indicator::class]['_path'] ?? '';

        $path = Environment::getPublicPath().'/'.$defaultPath;
        if (!file_exists($path)) {
            GeneralUtility::mkdir_deep($path);
        }

        if (!$publicPath) {
            return $defaultPath;
        }

        return $path;
    }

    /**
     * @return array<class-string<IndicatorInterface>, array<string|int, mixed|ModifierInterface>>
     */
    public static function getIndicatorConfiguration(): array
    {
        return Handler::resolveIndicators();
    }

    public static function isCurrentIndicator(string $indicatorClass): bool
    {
        return array_key_exists(
            $indicatorClass,
            self::getIndicatorConfiguration(),
        );
    }

    /**
     * Replaces the "%context%" placeholder with the current application context.
     */
    public static function replaceContextPlaceholder(string $value): string
    {
        if (!str_contains($value, '%context%')) {
            return $value;
        }

        return str_replace('%context%', Environment::getContext()->__toString(), $value);
    }

    public static function isExtensionFeatureEnabled(string $path): bool
    {
        try {
            return (bool) GeneralUtility::makeInstance(ExtensionConfiguration::class)
                ->get(Configuration::EXT_KEY, $path);
        } catch (Exception) {
            return false;
        }
    }

    public static function isMinimumTypo3Version(int $version): bool
    {
        return GeneralUtility::makeInstance(Typo3Version::class)->getMajorVersion() >= $version;
    }

    /**
     * The fifth parameter enables the backend nonce/CSP handling on both
     * supported core versions, but under a different name and with an
     * incompatible third parameter: TYPO3 v13 requires a real bool there
     * (`$compress`) and calls the fifth one `$useNonce`, while v14 ignores
     * the third one entirely (`mixed $_`) and calls the fifth one `$csp`.
     * Passing `true` positionally for the third parameter is v13's own
     * default, so behaviour does not change there, and v14 ignores it.
     */
    public static function addNonceGuardedJsInlineCode(PageRenderer $pageRenderer, string $name, string $block, bool $footer = false): void
    {
        if ($footer) {
            $pageRenderer->addJsFooterInlineCode($name, $block, true, false, true);
        } else {
            $pageRenderer->addJsInlineCode($name, $block, true, false, true);
        }
    }

    /**
     * @see self::addNonceGuardedJsInlineCode()
     */
    public static function addNonceGuardedCssInlineBlock(PageRenderer $pageRenderer, string $name, string $block): void
    {
        $pageRenderer->addCssInlineBlock($name, $block, false, false, true);
    }

    public static function supportFormat(ImageManagerInterface $manager, string $format): bool
    {
        if ('ico' === $format || 'svg' === $format) {
            return true;
        }

        if (method_exists($manager, 'driver')) {
            return $manager->driver()->supports($format);
        }

        // @codeCoverageIgnoreStart
        // v4-only path: driver() is only absent from ImageManagerInterface with
        // intervention/image ^4.0 installed; this project's test environment runs against v3.
        return true;
        // @codeCoverageIgnoreEnd
    }
}
