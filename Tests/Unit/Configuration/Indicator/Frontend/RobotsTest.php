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

use PHPUnit\Framework\TestCase;

use function dirname;

/**
 * RobotsTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class RobotsTest extends TestCase
{
    /**
     * Guards the one property of this indicator that cannot be walked back:
     * shipping it in a preset would silently add noindex to installations that
     * never asked for it. Registering a default value under 'defaults' is
     * fine - only an instantiation inside a preset would activate it.
     */
    public function testIsNotPartOfTheDefaultPresets(): void
    {
        $extLocalconf = file_get_contents(dirname(__DIR__, 5).'/ext_localconf.php');

        self::assertIsString($extLocalconf);
        self::assertStringNotContainsString('new Indicator\Frontend\Robots(', $extLocalconf);
    }
}
