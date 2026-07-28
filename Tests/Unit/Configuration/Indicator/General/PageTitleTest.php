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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\General;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\General\PageTitle;
use PHPUnit\Framework\TestCase;

/**
 * PageTitleTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class PageTitleTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $indicator = new PageTitle();
        self::assertInstanceOf(PageTitle::class, $indicator);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['prefix' => '[STG] ', 'suffix' => ' (STG)'];
        $indicator = new PageTitle($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = ['prefix' => '[%context%] '];
        $indicator = new PageTitle($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        self::assertInstanceOf(AbstractIndicator::class, new PageTitle());
    }

    public function testImplementsIndicatorInterface(): void
    {
        self::assertInstanceOf(IndicatorInterface::class, new PageTitle());
    }
}
