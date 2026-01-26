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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Utility;

use Exception;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ConfigurationUtility;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationUtilityTest extends TestCase
{
    public function testConfigByContextThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionCode(5404480452);
        $this->expectExceptionMessage('deprecated and no longer support');

        ConfigurationUtility::configByContext('Development');
    }

    public function testConfigByContextContainsCorrectMethodReference(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Handler::addIndicator/');

        ConfigurationUtility::configByContext('Development');
    }
}
