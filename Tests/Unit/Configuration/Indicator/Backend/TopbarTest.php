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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar;
use PHPUnit\Framework\TestCase;

/**
 * TopbarTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TopbarTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $topbar = new Topbar();
        self::assertInstanceOf(Topbar::class, $topbar);
    }

    public function testExtendsAbstractIndicator(): void
    {
        $topbar = new Topbar();
        self::assertInstanceOf(AbstractIndicator::class, $topbar);
    }

    public function testImplementsIndicatorInterface(): void
    {
        $topbar = new Topbar();
        self::assertInstanceOf(IndicatorInterface::class, $topbar);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['text' => 'Development'];
        $topbar = new Topbar($config);
        self::assertInstanceOf(Topbar::class, $topbar);
    }
}
