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
use KonradMichalik\Typo3EnvironmentIndicator\Backend\ToolbarItems\ContextItem;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Toolbar;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ColorUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ContextItemTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextItemTest extends FunctionalTestCase
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
                'current' => [Toolbar::class => ['color' => '#bd593a']],
                'resolved' => true,
            ]],
        ]);

        self::assertTrue($this->createSubject()->checkAccess());
    }

    public function testGetItemReturnsEmptyStringWhenNotConfigured(): void
    {
        self::assertSame('', $this->createSubject()->getItem());
    }

    public function testGetItemRendersContextNameByDefault(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Toolbar::class => ['color' => '#bd593a']],
            'resolved' => true,
        ]]]);

        $result = $this->createSubject()->getItem();

        self::assertStringContainsString('data-context="Testing"', $result);
        self::assertStringContainsString('title="Application context"', $result);
        self::assertStringContainsString('background-color:#bd593a', $result);
        self::assertStringContainsString('color:'.ColorUtility::getOptimalTextColor('#bd593a'), $result);
    }

    public function testGetItemRendersConfiguredTextIconAndDescription(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Toolbar::class => [
                'color' => '#bd593a',
                'text' => 'Staging',
                'description' => '  Custom description  ',
                'icon' => ['context' => 'actions-info'],
            ]],
            'resolved' => true,
        ]]]);

        $result = $this->createSubject()->getItem();

        self::assertStringContainsString('data-context="Staging"', $result);
        self::assertStringContainsString('title="Application context: Custom description"', $result);
        self::assertStringContainsString('actions-info', $result);
    }

    public function testGetIndexReturnsConfiguredValue(): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Toolbar::class => ['index' => 5]],
            'resolved' => true,
        ]]]);

        self::assertSame(5, $this->createSubject()->getIndex());
    }

    public function testGetIndexReturnsZeroByDefault(): void
    {
        self::assertSame(0, $this->createSubject()->getIndex());
    }

    private function createSubject(): ContextItem
    {
        return new ContextItem($this->get(ExtensionConfiguration::class));
    }
}
