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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\TypoScript;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\TypoScript\TechnicalContextConditionFunctionsProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * TechnicalContextConditionFunctionsProviderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TechnicalContextConditionFunctionsProviderTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [];
    }

    public function testConstructorWithExtensionConfiguration(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $provider = new TechnicalContextConditionFunctionsProvider($extensionConfiguration);
        self::assertInstanceOf(TechnicalContextConditionFunctionsProvider::class, $provider);
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $provider = new TechnicalContextConditionFunctionsProvider($extensionConfiguration);
        $functions = $provider->getFunctions();
        self::assertCount(1, $functions);
    }

    public function testGetFunctionsReturnsExpressionFunction(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $provider = new TechnicalContextConditionFunctionsProvider($extensionConfiguration);
        $functions = $provider->getFunctions();
        self::assertInstanceOf(ExpressionFunction::class, $functions[0]);
    }

    public function testExpressionFunctionHasCorrectName(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $provider = new TechnicalContextConditionFunctionsProvider($extensionConfiguration);
        $functions = $provider->getFunctions();
        $function = $functions[0];
        self::assertEquals('enableTechnicalContext', $function->getName());
    }

    public function testImplementsExpressionFunctionProviderInterface(): void
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $provider = new TechnicalContextConditionFunctionsProvider($extensionConfiguration);
        self::assertInstanceOf(\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface::class, $provider);
    }
}
