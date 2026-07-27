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

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
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
#[WithTypo3ConfVars([
    'EXTCONF' => [Configuration::EXT_KEY => ['current' => []]],
    'EXTENSIONS' => [Configuration::EXT_KEY => []],
])]
class TechnicalContextConditionFunctionsProviderTest extends TestCase
{
    public function testConstructorWithExtensionConfiguration(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        self::assertInstanceOf(TechnicalContextConditionFunctionsProvider::class, $provider);
    }

    public function testGetFunctionsReturnsArray(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        $functions = $provider->getFunctions();
        self::assertCount(1, $functions);
    }

    public function testGetFunctionsReturnsExpressionFunction(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        $functions = $provider->getFunctions();
        self::assertInstanceOf(ExpressionFunction::class, $functions[0]);
    }

    public function testExpressionFunctionHasCorrectName(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        $functions = $provider->getFunctions();
        $function = $functions[0];
        self::assertEquals('enableTechnicalContext', $function->getName());
    }

    public function testImplementsExpressionFunctionProviderInterface(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        self::assertInstanceOf(\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface::class, $provider);
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['context' => false]]]])]
    public function testEvaluatorReturnsFalseWhenFeatureDisabled(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        $evaluator = $provider->getFunctions()[0]->getEvaluator();

        self::assertFalse($evaluator([]));
    }

    #[WithTypo3ConfVars(['EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['context' => true]]]])]
    public function testEvaluatorReturnsFalseWhenFeatureEnabledButNoCurrentHintIndicator(): void
    {
        $provider = new TechnicalContextConditionFunctionsProvider(new ExtensionConfiguration());
        $evaluator = $provider->getFunctions()[0]->getEvaluator();

        self::assertFalse($evaluator([]));
    }
}
