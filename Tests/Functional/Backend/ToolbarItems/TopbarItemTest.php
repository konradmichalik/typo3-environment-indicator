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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Backend\ToolbarItems;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\TopbarItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ColorUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * TopbarItemTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TopbarItemTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testCheckAccessReturnsTrueWhenApplicableInTestingContext(): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['context' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Topbar::class => ['color' => '#bd593a']],
                'resolved' => true,
            ]],
        ]);

        self::assertTrue($this->createSubject($this->createStub(PageRenderer::class))->checkAccess());
    }

    public function testGetItemInjectsNoCssWhenNotConfigured(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::never())->method('addCssInlineBlock');

        self::assertSame('', $this->createSubject($pageRenderer)->getItem());
    }

    public function testGetItemInjectsCssWithoutJsWhenDescriptionMissing(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Topbar::class => ['color' => '#bd593a']],
            'resolved' => true,
        ]]]);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())
            ->method('addCssInlineBlock')
            ->with(
                Configuration::EXT_KEY.'_topbar',
                self::logicalAnd(
                    self::stringContains('background-color: #bd593a;'),
                    self::stringContains('color: '.ColorUtility::getOptimalTextColor('#bd593a').';'),
                    self::stringContains('color: '.ColorUtility::getOptimalTextColor('#bd593a', 0.8).';'),
                ),
            );
        $pageRenderer->expects(self::never())->method('addJsFooterInlineCode');

        self::assertSame('', $this->createSubject($pageRenderer)->getItem());
    }

    public function testGetItemInjectsTitleJsWhenDescriptionConfigured(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Topbar::class => ['color' => '#bd593a', 'description' => '  Staging environment  ']],
            'resolved' => true,
        ]]]);

        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())->method('addCssInlineBlock');
        $pageRenderer->expects(self::once())
            ->method('addJsFooterInlineCode')
            ->with(
                Configuration::EXT_KEY.'_topbar',
                self::stringContains('Staging environment'),
                true,
                false,
                true,
            );

        self::assertSame('', $this->createSubject($pageRenderer)->getItem());
    }

    private function createSubject(PageRenderer $pageRenderer): TopbarItem
    {
        return new TopbarItem($this->get(ExtensionConfiguration::class), $pageRenderer);
    }
}
