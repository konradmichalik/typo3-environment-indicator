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
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
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

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
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

    public function testGetTitleReturnsConfiguredText(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            Configuration\Indicator\Frontend\Hint::class => [
                'text' => 'My Environment',
            ],
        ];

        self::assertSame('My Environment', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenNoRequest(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenRoutingIsNotPageArguments(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(null);

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenSiteFinderIsMissing(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(new PageArguments(1, '0', []));

        self::assertSame('', (new ContextUtility())->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenSiteIsNotFound(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(new PageArguments(42, '0', []));

        $siteFinder = $this->createMock(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')->willThrowException(new SiteNotFoundException());

        self::assertSame('', (new ContextUtility($siteFinder))->getTitle());
    }

    public function testGetTitleReturnsWebsiteTitleFromSiteConfiguration(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(new PageArguments(1, '0', []));

        $site = $this->createMock(Site::class);
        $site->method('getConfiguration')->willReturn(['websiteTitle' => 'Production Site']);

        $siteFinder = $this->createMock(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')->willReturn($site);

        self::assertSame('Production Site', (new ContextUtility($siteFinder))->getTitle());
    }

    public function testGetTitleFallsBackToSiteIdentifierWithoutWebsiteTitle(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->createRequestWithRouting(new PageArguments(1, '0', []));

        $site = $this->createMock(Site::class);
        $site->method('getConfiguration')->willReturn([]);
        $site->method('getIdentifier')->willReturn('main');

        $siteFinder = $this->createMock(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')->willReturn($site);

        self::assertSame('main', (new ContextUtility($siteFinder))->getTitle());
    }

    private function createRequestWithRouting(?PageArguments $routing): ServerRequestInterface
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getAttribute')->with('routing')->willReturn($routing);

        return $request;
    }
}
