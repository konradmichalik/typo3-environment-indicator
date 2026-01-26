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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Backend;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Toolbar;
use PHPUnit\Framework\TestCase;

/**
 * ToolbarTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ToolbarTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $toolbar = new Toolbar();
        self::assertInstanceOf(Toolbar::class, $toolbar);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = [
            'color' => '#ff0000',
            'name' => 'Development',
            'index' => 100,
        ];
        $toolbar = new Toolbar($config);
        self::assertEquals($config, $toolbar->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = [
            'color' => '#00ff00',
            'name' => 'Testing',
            'index' => 200,
        ];
        $toolbar = new Toolbar($config);
        self::assertEquals($config, $toolbar->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        $toolbar = new Toolbar();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator::class, $toolbar);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $toolbar = new Toolbar();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface::class, $toolbar);
    }
}
