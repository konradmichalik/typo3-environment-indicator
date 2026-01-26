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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ContextUtility;
use PHPUnit\Framework\TestCase;

/**
 * ContextUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextUtilityTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
    }

    public function testGetColorReturnsTransparentWhenNoConfiguration(): void
    {
        $contextUtility = new ContextUtility();
        $color = $contextUtility->getColor();
        self::assertEquals('transparent', $color);
    }

    public function testGetTextColorReturnsColorUtilityResult(): void
    {
        $contextUtility = new ContextUtility();
        $textColor = $contextUtility->getTextColor();
        self::assertStringStartsWith('rgba(', $textColor);
    }

    public function testGetPositionXReturnsCorrectFormat(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Configuration\Indicator\Frontend\Hint::class => [
                'position' => 'top right',
            ],
        ];

        $contextUtility = new ContextUtility();
        $positionX = $contextUtility->getPositionX();
        self::assertEquals('top:0', $positionX);
    }

    public function testGetPositionYReturnsCorrectFormat(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Configuration\Indicator\Frontend\Hint::class => [
                'position' => 'bottom left',
            ],
        ];

        $contextUtility = new ContextUtility();
        $positionY = $contextUtility->getPositionY();
        self::assertEquals('left:0', $positionY);
    }
}
