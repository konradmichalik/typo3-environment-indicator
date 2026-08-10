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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\HttpHeader;
use PHPUnit\Framework\TestCase;

/**
 * HttpHeaderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class HttpHeaderTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $indicator = new HttpHeader();
        self::assertSame([], $indicator->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = ['name' => 'X-Environment', 'value' => 'Staging'];
        $indicator = new HttpHeader($config);
        self::assertSame($config, $indicator->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        self::assertInstanceOf(AbstractIndicator::class, new HttpHeader());
    }

    public function testImplementsIndicatorInterface(): void
    {
        self::assertInstanceOf(IndicatorInterface::class, new HttpHeader());
    }
}
