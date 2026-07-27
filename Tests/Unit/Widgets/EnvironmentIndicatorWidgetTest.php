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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Widgets;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Widgets\EnvironmentIndicatorWidget;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Dashboard\Widgets\{ButtonProviderInterface, WidgetConfigurationInterface};

/**
 * EnvironmentIndicatorWidgetTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => null]]])]
class EnvironmentIndicatorWidgetTest extends TestCase
{
    public function testConstructorWithConfiguration(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration);
        self::assertInstanceOf(EnvironmentIndicatorWidget::class, $widget);
    }

    public function testConstructorWithButtonProvider(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $buttonProvider = $this->createStub(ButtonProviderInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration, $buttonProvider);
        self::assertInstanceOf(EnvironmentIndicatorWidget::class, $widget);
    }

    public function testConstructorWithOptions(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $options = ['test' => 'value'];
        $widget = new EnvironmentIndicatorWidget($configuration, null, $options);
        self::assertInstanceOf(EnvironmentIndicatorWidget::class, $widget);
    }

    public function testGetOptionsReturnsOptions(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $options = ['test' => 'value', 'another' => 'option'];
        $widget = new EnvironmentIndicatorWidget($configuration, null, $options);
        self::assertEquals($options, $widget->getOptions());
    }

    public function testGetOptionsReturnsEmptyArrayWhenNoOptions(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration);
        self::assertEquals([], $widget->getOptions());
    }

    public function testGetCssFilesReturnsCorrectPath(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration);
        $cssFiles = $widget->getCssFiles();
        self::assertCount(1, $cssFiles);
        self::assertEquals('EXT:typo3_environment_indicator/Resources/Public/Css/Widget.css', $cssFiles[0]);
    }

    public function testImplementsWidgetInterface(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration);
        self::assertInstanceOf(\TYPO3\CMS\Dashboard\Widgets\WidgetInterface::class, $widget);
    }

    public function testImplementsAdditionalCssInterface(): void
    {
        $configuration = $this->createStub(WidgetConfigurationInterface::class);
        $widget = new EnvironmentIndicatorWidget($configuration);
        self::assertInstanceOf(\TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface::class, $widget);
    }
}
