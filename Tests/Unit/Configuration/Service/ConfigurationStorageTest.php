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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Service;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service\ConfigurationStorage;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationStorageTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationStorageTest extends TestCase
{
    private ConfigurationStorage $configurationStorage;

    protected function setUp(): void
    {
        $this->configurationStorage = new ConfigurationStorage();

        // Clear global state
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
    }

    protected function tearDown(): void
    {
        // Clear global state
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
    }

    public function testAddConfiguration(): void
    {
        $configuration = ['test' => 'value'];
        $this->configurationStorage->addConfiguration($configuration);

        $configurations = $this->configurationStorage->getConfigurations();
        self::assertCount(1, $configurations);
        self::assertEquals($configuration, $configurations[0]);
    }

    public function testGetConfigurationsReturnsEmptyArrayWhenNotSet(): void
    {
        $configurations = $this->configurationStorage->getConfigurations();
        self::assertEmpty($configurations);
    }

    public function testHasConfigurationsReturnsFalseWhenNotSet(): void
    {
        self::assertFalse($this->configurationStorage->hasConfigurations());
    }

    public function testHasConfigurationsReturnsTrueWhenSet(): void
    {
        $this->configurationStorage->addConfiguration(['test' => 'value']);
        self::assertTrue($this->configurationStorage->hasConfigurations());
    }

    public function testGetCurrentIndicatorsReturnsEmptyArrayWhenNotSet(): void
    {
        $indicators = $this->configurationStorage->getCurrentIndicators();
        self::assertEmpty($indicators);
    }

    public function testHasCurrentIndicatorsReturnsFalseWhenNotSet(): void
    {
        self::assertFalse($this->configurationStorage->hasCurrentIndicators());
    }

    public function testSetCurrentIndicator(): void
    {
        $this->configurationStorage->setCurrentIndicator('TestClass', ['config' => 'value']);

        $indicators = $this->configurationStorage->getCurrentIndicators();
        self::assertArrayHasKey('TestClass', $indicators);
        self::assertEquals(['config' => 'value'], $indicators['TestClass']);
        self::assertTrue($this->configurationStorage->hasCurrentIndicators());
    }

    public function testMergeCurrentIndicatorWithExisting(): void
    {
        $this->configurationStorage->setCurrentIndicator('TestClass', ['config1' => 'value1']);
        $this->configurationStorage->mergeCurrentIndicator('TestClass', ['config2' => 'value2']);

        $indicators = $this->configurationStorage->getCurrentIndicators();
        self::assertArrayHasKey('TestClass', $indicators);
        self::assertEquals([
            'config1' => 'value1',
            'config2' => 'value2',
        ], $indicators['TestClass']);
    }

    public function testMergeCurrentIndicatorWithoutExisting(): void
    {
        $this->configurationStorage->mergeCurrentIndicator('TestClass', ['config' => 'value']);

        $indicators = $this->configurationStorage->getCurrentIndicators();
        self::assertArrayHasKey('TestClass', $indicators);
        self::assertEquals(['config' => 'value'], $indicators['TestClass']);
    }
}
