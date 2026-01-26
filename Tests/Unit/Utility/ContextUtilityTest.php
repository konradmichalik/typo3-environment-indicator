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
use TYPO3\CMS\Core\Site\SiteFinder;

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
        $siteFinder = $this->createStub(SiteFinder::class);
        $contextUtility = new ContextUtility($siteFinder);
        $color = $contextUtility->getColor();
        self::assertEquals('transparent', $color);
    }

    public function testGetTextColorReturnsColorUtilityResult(): void
    {
        $siteFinder = $this->createStub(SiteFinder::class);
        $contextUtility = new ContextUtility($siteFinder);
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

        $siteFinder = $this->createStub(SiteFinder::class);
        $contextUtility = new ContextUtility($siteFinder);
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

        $siteFinder = $this->createStub(SiteFinder::class);
        $contextUtility = new ContextUtility($siteFinder);
        $positionY = $contextUtility->getPositionY();
        self::assertEquals('left:0', $positionY);
    }
}
