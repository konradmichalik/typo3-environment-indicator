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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Widget;
use PHPUnit\Framework\TestCase;

/**
 * WidgetTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class WidgetTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $widget = new Widget();
        self::assertInstanceOf(Widget::class, $widget);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = [
            'color' => '#ff0000',
            'name' => 'Development',
            'textSize' => '24px',
        ];
        $widget = new Widget($config);
        self::assertEquals($config, $widget->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = [
            'color' => '#00ff00',
            'name' => 'Testing',
            'textSize' => '18px',
        ];
        $widget = new Widget($config);
        self::assertEquals($config, $widget->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        $widget = new Widget();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator::class, $widget);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $widget = new Widget();
        self::assertInstanceOf(\KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface::class, $widget);
    }
}
