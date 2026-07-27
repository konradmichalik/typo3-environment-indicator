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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Image\Modifier;

use KonradMichalik\Ttt\Contract\ConfigurationValidationContract;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ColorizeModifier;

/**
 * ColorizeModifierValidationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ColorizeModifierValidationTest extends ConfigurationValidationContract
{
    protected function isValid(array $configuration): bool
    {
        return (new ColorizeModifier($this->validConfiguration()))->validateConfiguration($configuration);
    }

    protected function validConfiguration(): array
    {
        return ['color' => '#ff0000'];
    }

    protected function schema(): array
    {
        return ['color' => 'string', 'opacity?' => 'float:0..1', 'brightness?' => 'float', 'contrast?' => 'float'];
    }
}
