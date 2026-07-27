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
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ReplaceModifier;

/**
 * ReplaceModifierValidationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ReplaceModifierValidationTest extends ConfigurationValidationContract
{
    protected function isValid(array $configuration): bool
    {
        return (new ReplaceModifier($this->validConfiguration()))->validateConfiguration($configuration);
    }

    protected function validConfiguration(): array
    {
        return ['path' => 'EXT:test.png'];
    }

    protected function schema(): array
    {
        return ['path' => 'string'];
    }
}
