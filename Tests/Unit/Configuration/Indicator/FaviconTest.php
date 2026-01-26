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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Enum\Scope;
use PHPUnit\Framework\TestCase;

/**
 * FaviconTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FaviconTest extends TestCase
{
    protected function setUp(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    public function testConstructorWithDefaultValues(): void
    {
        $favicon = new Favicon();
        self::assertInstanceOf(Favicon::class, $favicon);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['color' => '#ff0000'];
        $favicon = new Favicon($config);
        self::assertEquals($config, $favicon->getConfiguration());
    }

    public function testGetConfigurationWithGlobalScope(): void
    {
        $config = ['color' => '#ff0000'];
        $favicon = new Favicon($config, Scope::Global);
        self::assertEquals($config, $favicon->getConfiguration());
    }

    public function testGetConfigurationWithNoRequest(): void
    {
        $config = ['color' => '#ff0000'];
        $favicon = new Favicon($config, Scope::Backend);
        self::assertEquals([], $favicon->getConfiguration());
    }

    public function testGetConfigurationWithFrontendScope(): void
    {
        $config = ['color' => '#ff0000'];
        $favicon = new Favicon($config, Scope::Frontend);
        self::assertEquals([], $favicon->getConfiguration());
    }
}
