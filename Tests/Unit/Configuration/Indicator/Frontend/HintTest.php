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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Frontend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Hint;
use PHPUnit\Framework\TestCase;

/**
 * HintTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class HintTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $hint = new Hint();
        self::assertInstanceOf(Hint::class, $hint);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = [
            'color' => '#ff0000',
            'text' => 'Development',
            'position' => 'bottom right',
        ];
        $hint = new Hint($config);
        self::assertEquals($config, $hint->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = [
            'color' => '#00ff00',
            'text' => 'Testing',
            'position' => 'top left',
        ];
        $hint = new Hint($config);
        self::assertEquals($config, $hint->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        $hint = new Hint();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator::class, $hint);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $hint = new Hint();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface::class, $hint);
    }
}
