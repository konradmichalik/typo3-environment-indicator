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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator\Mail;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Mail\SubjectPrefix;
use PHPUnit\Framework\TestCase;

/**
 * SubjectPrefixTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class SubjectPrefixTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $indicator = new SubjectPrefix();
        self::assertInstanceOf(SubjectPrefix::class, $indicator);
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['prefix' => '[STG] ', 'header' => 'X-Environment'];
        $indicator = new SubjectPrefix($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testGetConfigurationReturnsConfiguration(): void
    {
        $config = ['prefix' => '[TEST] '];
        $indicator = new SubjectPrefix($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testInheritsFromAbstractIndicator(): void
    {
        self::assertInstanceOf(AbstractIndicator::class, new SubjectPrefix());
    }

    public function testImplementsIndicatorInterface(): void
    {
        self::assertInstanceOf(IndicatorInterface::class, new SubjectPrefix());
    }
}
