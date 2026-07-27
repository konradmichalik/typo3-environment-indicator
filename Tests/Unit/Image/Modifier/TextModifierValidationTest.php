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
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier;

/**
 * TextModifierValidationTest.
 *
 * Covers the flat top-level keys via the contract; the nested "stroke"
 * sub-config doesn't fit the schema DSL and stays a manual test in
 * TextModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class TextModifierValidationTest extends ConfigurationValidationContract
{
    protected function isValid(array $configuration): bool
    {
        return (new TextModifier($this->validConfiguration()))->validateConfiguration($configuration);
    }

    protected function validConfiguration(): array
    {
        return ['text' => 'Test', 'color' => '#fff'];
    }

    protected function schema(): array
    {
        return ['text' => 'string', 'color' => 'string', 'font?' => 'string', 'position?' => 'enum:top|bottom'];
    }
}
