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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\ExpressionLanguage;

use KonradMichalik\Typo3EnvironmentIndicator\ExpressionLanguage\TechnicalContextTypoScriptConditionProvider;
use KonradMichalik\Typo3EnvironmentIndicator\TypoScript\TechnicalContextConditionFunctionsProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

/**
 * TechnicalContextTypoScriptConditionProviderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TechnicalContextTypoScriptConditionProviderTest extends TestCase
{
    public function testConstructorSetsExpressionLanguageProviders(): void
    {
        $provider = new TechnicalContextTypoScriptConditionProvider();
        self::assertInstanceOf(TechnicalContextTypoScriptConditionProvider::class, $provider);
    }

    public function testExtendsAbstractProvider(): void
    {
        $provider = new TechnicalContextTypoScriptConditionProvider();
        self::assertInstanceOf(AbstractProvider::class, $provider);
    }

    public function testExpressionLanguageProvidersContainsTechnicalContextConditionFunctionsProvider(): void
    {
        $provider = new TechnicalContextTypoScriptConditionProvider();
        $reflection = new ReflectionClass($provider);
        $property = $reflection->getProperty('expressionLanguageProviders');
        $providers = $property->getValue($provider);

        self::assertIsArray($providers);
        self::assertCount(1, $providers);
        self::assertEquals(TechnicalContextConditionFunctionsProvider::class, $providers[0]);
    }
}
