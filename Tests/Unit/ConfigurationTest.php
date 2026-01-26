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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationTest extends TestCase
{
    public function testExtensionKeyConstant(): void
    {
        self::assertEquals('typo3_environment_indicator', Configuration::EXT_KEY);
    }

    public function testExtensionNameConstant(): void
    {
        self::assertEquals('Typo3EnvironmentIndicator', Configuration::EXT_NAME);
    }
}
