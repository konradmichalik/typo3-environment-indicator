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

use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\AbstractModifier;
use PHPUnit\Framework\TestCase;

/**
 * AbstractModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class AbstractModifierTest extends TestCase
{
    protected function setUp(): void {}

    public function testInstantiationWithoutRequiredKeys(): void
    {
        $modifier = new
/**
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class([]) extends AbstractModifier {
    /**
     * @return array<int, string>
     */
    public function getRequiredConfigurationKeys(): array
    {
        return [];
    }
};

        self::assertInstanceOf(AbstractModifier::class, $modifier);
    }

    public function testInstantiationWithRequiredKeys(): void
    {
        $modifier = new
/**
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class(['required_key' => 'value']) extends AbstractModifier {
    /**
     * @return array<int, string>
     */
    public function getRequiredConfigurationKeys(): array
    {
        return ['required_key'];
    }
};

        self::assertInstanceOf(AbstractModifier::class, $modifier);
    }
}
