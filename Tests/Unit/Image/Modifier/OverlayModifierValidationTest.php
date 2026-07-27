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
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\OverlayModifier;

/**
 * OverlayModifierValidationTest.
 *
 * "size" is only valid in (0, 1] (0 itself is rejected), which the
 * schema's inclusive "0..1" range can't express exactly, so the
 * size-equals-zero boundary stays a manual test in OverlayModifierTest,
 * alongside the enum-style "position" value check.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class OverlayModifierValidationTest extends ConfigurationValidationContract
{
    protected function isValid(array $configuration): bool
    {
        return (new OverlayModifier($this->validConfiguration()))->validateConfiguration($configuration);
    }

    protected function validConfiguration(): array
    {
        return ['path' => 'EXT:test.png', 'size' => 0.5, 'position' => 'center', 'padding' => 0.1];
    }

    protected function schema(): array
    {
        return ['path' => 'string', 'size' => 'float:0..1', 'position' => 'string', 'padding' => 'float:0..1'];
    }
}
