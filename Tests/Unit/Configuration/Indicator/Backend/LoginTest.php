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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Login;
use PHPUnit\Framework\TestCase;

/**
 * LoginTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class LoginTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $login = new Login();
        self::assertInstanceOf(Login::class, $login);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['text' => 'STAGING', 'color' => '#00ACC1', 'position' => 'bottom'];
        $login = new Login($config);
        self::assertEquals($config, $login->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = ['text' => 'DEV', 'description' => 'Data is overwritten nightly'];
        $login = new Login($config);
        self::assertEquals($config, $login->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        self::assertInstanceOf(AbstractIndicator::class, new Login());
    }

    public function testImplementsIndicatorInterface(): void
    {
        self::assertInstanceOf(IndicatorInterface::class, new Login());
    }
}
