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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_key_exists;
use function is_array;

/**
 * AbstractIndicator.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
abstract class AbstractIndicator
{
    /**
     * @param array<string|int, mixed|ModifierInterface> $configuration
     */
    public function __construct(protected array $configuration = [], protected ?ServerRequestInterface $request = null)
    {
        $this->configuration = $this->mergeGlobalConfiguration($this->configuration);
    }

    /**
     * @return array<string|int, mixed|ModifierInterface>
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param array<string|int, mixed|ModifierInterface> $configuration
     *
     * @return array<string|int, mixed|ModifierInterface>
     */
    final protected function mergeGlobalConfiguration(array $configuration): array
    {
        $globalConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['defaults'] ?? [];

        return is_array($globalConfiguration) && array_key_exists(static::class, $globalConfiguration) ? array_replace_recursive($globalConfiguration[static::class], $configuration) : $configuration;
    }
}
