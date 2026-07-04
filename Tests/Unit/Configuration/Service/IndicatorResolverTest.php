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

use Exception;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{Favicon, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service\{ConfigurationStorage, IndicatorResolver, TriggerEvaluator};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * IndicatorResolverTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class IndicatorResolverTest extends TestCase
{
    public function testResolveIndicatorsReturnsCachedIndicators(): void
    {
        $storage = $this->createStub(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(true);
        $storage->method('getCurrentIndicators')->willReturn([Favicon::class => ['color' => 'red']]);

        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);
        $result = $resolver->resolveIndicators();

        self::assertEquals([Favicon::class => ['color' => 'red']], $result);
    }

    public function testResolveIndicatorsProcessesConfigurationsWhenNotCached(): void
    {
        $indicator = $this->createStub(IndicatorInterface::class);
        $indicator->method('getConfiguration')->willReturn(['test' => 'value']);

        $configurations = [
            [
                'triggers' => [],
                'indicators' => [$indicator],
            ],
        ];

        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(false);
        $storage->method('getConfigurations')->willReturn($configurations);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::once())
            ->method('mergeCurrentIndicator')
            ->with($indicator::class, ['test' => 'value']);

        $evaluator = $this->createStub(TriggerEvaluator::class);
        $evaluator->method('evaluateTriggers')->willReturn(true);

        $storage->expects(self::once())->method('markResolved');

        $resolver = new IndicatorResolver($storage, $evaluator);
        $resolver->resolveIndicators();
    }

    public function testResolveIndicatorsMemoizesWhenAlreadyResolved(): void
    {
        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(true);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::never())->method('getConfigurations');
        $storage->expects(self::never())->method('markResolved');

        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);

        self::assertSame([], $resolver->resolveIndicators());
    }

    public function testResolveIndicatorsSkipsConfigurationWhenTriggersDoNotPass(): void
    {
        $indicator = $this->createStub(IndicatorInterface::class);
        $trigger = $this->createStub(TriggerInterface::class);

        $configurations = [
            [
                'triggers' => [$trigger],
                'indicators' => [$indicator],
            ],
        ];

        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(false);
        $storage->method('getConfigurations')->willReturn($configurations);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::never())
            ->method('mergeCurrentIndicator');

        $evaluator = $this->createStub(TriggerEvaluator::class);
        $evaluator->method('evaluateTriggers')->willReturn(false);

        $resolver = new IndicatorResolver($storage, $evaluator);
        $resolver->resolveIndicators();
    }

    public function testResolveIndicatorsSkipsConfigurationWhenNoIndicators(): void
    {
        $configurations = [
            [
                'triggers' => [],
                'indicators' => [],
            ],
        ];

        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(false);
        $storage->method('getConfigurations')->willReturn($configurations);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::never())
            ->method('mergeCurrentIndicator');

        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);
        $resolver->resolveIndicators();
    }

    public function testResolveIndicatorsHandlesExceptionInIndicatorProcessing(): void
    {
        $indicator = $this->createStub(IndicatorInterface::class);
        $indicator->method('getConfiguration')->willThrowException(new Exception('Test exception'));

        $configurations = [
            [
                'triggers' => [],
                'indicators' => [$indicator],
            ],
        ];

        $storage = $this->createStub(ConfigurationStorage::class);
        $storage->method('isResolved')->willReturn(false);
        $storage->method('getConfigurations')->willReturn($configurations);
        $storage->method('getCurrentIndicators')->willReturn([]);

        $evaluator = $this->createStub(TriggerEvaluator::class);
        $evaluator->method('evaluateTriggers')->willReturn(true);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('warning')
            ->with(
                'Indicator processing failed: {message}',
                self::callback(static fn (array $context): bool => 'Test exception' === $context['message'] && $context['exception'] instanceof Exception),
            );

        $resolver = new IndicatorResolver($storage, $evaluator, $logger);
        $result = $resolver->resolveIndicators();

        self::assertEquals([], $result);
    }

    public function testValidateIndicatorsReturnsTrueForValidIndicators(): void
    {
        $storage = $this->createStub(ConfigurationStorage::class);
        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);

        $favicon = new Favicon();
        self::assertTrue($resolver->validateIndicators([$favicon]));
    }

    public function testValidateIndicatorsReturnsTrueForEmptyArray(): void
    {
        $storage = $this->createStub(ConfigurationStorage::class);
        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);

        self::assertTrue($resolver->validateIndicators([]));
    }

    public function testValidateIndicatorsReturnsFalseForInvalidIndicators(): void
    {
        $storage = $this->createStub(ConfigurationStorage::class);
        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);

        self::assertFalse($resolver->validateIndicators(['invalid']));
    }

    public function testValidateIndicatorsReturnsFalseWhenOneIndicatorIsInvalid(): void
    {
        $storage = $this->createStub(ConfigurationStorage::class);
        $evaluator = $this->createStub(TriggerEvaluator::class);

        $resolver = new IndicatorResolver($storage, $evaluator);

        $favicon = new Favicon();
        $invalid = new stdClass();

        self::assertFalse($resolver->validateIndicators([$favicon, $invalid]));
    }
}
