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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Trigger;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext;
use PHPUnit\Framework\TestCase;

/**
 * ApplicationContextTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ApplicationContextTest extends TestCase
{
    public function testInstantiation(): void
    {
        $trigger = new ApplicationContext('Development');
        self::assertInstanceOf(ApplicationContext::class, $trigger);
    }

    public function testInstantiationWithMultipleContexts(): void
    {
        $trigger = new ApplicationContext('Development', 'Testing', 'Production');
        self::assertInstanceOf(ApplicationContext::class, $trigger);
    }

    public function testConstructorAcceptsVariadicArguments(): void
    {
        $trigger = new ApplicationContext('Development', 'Testing', 'Production/Staging');
        self::assertInstanceOf(ApplicationContext::class, $trigger);
    }
}
