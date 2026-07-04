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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{Backend, Favicon, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service\{ConfigurationStorage, IndicatorResolver, TriggerEvaluator};
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\{LoggerInterface, NullLogger};
use ReflectionProperty;
use stdClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

use function count;

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
        $storage->method('hasCurrentIndicators')->willReturn(true);
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
        $storage->method('hasCurrentIndicators')->willReturn(false);
        $storage->method('getConfigurations')->willReturn($configurations);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::once())
            ->method('mergeCurrentIndicator')
            ->with($indicator::class, ['test' => 'value']);

        $evaluator = $this->createStub(TriggerEvaluator::class);
        $evaluator->method('evaluateTriggers')->willReturn(true);

        $resolver = new IndicatorResolver($storage, $evaluator);
        $resolver->resolveIndicators();
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
        $storage->method('hasCurrentIndicators')->willReturn(false);
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
        $storage->method('hasCurrentIndicators')->willReturn(false);
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
        $storage->method('hasCurrentIndicators')->willReturn(false);
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

    public function testInstanceColorOverridesActivePlainColorIndicators(): void
    {
        $current = [
            Backend\Toolbar::class => ['color' => '#111111'],
            Backend\Widget::class => ['color' => '#111111'],
        ];

        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('hasCurrentIndicators')->willReturn(false);
        $storage->method('getConfigurations')->willReturn([]);
        $storage->method('getCurrentIndicators')->willReturn($current);

        $merged = [];
        $storage->method('mergeCurrentIndicator')
            ->willReturnCallback(static function (string $class, array $configuration) use (&$merged): void {
                $merged[$class][] = $configuration;
            });

        $resolver = new IndicatorResolver($storage, $this->createStub(TriggerEvaluator::class), new NullLogger(), $this->createInstanceConfiguration(['color' => '#ff0000']));
        $resolver->resolveIndicators();

        self::assertSame([['color' => '#ff0000']], $merged[Backend\Toolbar::class]);
        self::assertSame([['color' => '#ff0000']], $merged[Backend\Widget::class]);
    }

    public function testInstanceLabelReplacesImageIndicatorModifiersAndDerivesColor(): void
    {
        $current = [
            Favicon::class => [$this->createStub(IndicatorInterface::class)],
            Backend\Toolbar::class => ['color' => '#111111'],
        ];

        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('hasCurrentIndicators')->willReturn(false);
        $storage->method('getConfigurations')->willReturn([]);
        $storage->method('getCurrentIndicators')->willReturn($current);

        $storage->expects(self::once())
            ->method('setCurrentIndicator')
            ->with(
                Favicon::class,
                self::callback(static function (array $configuration): bool {
                    $modifier = $configuration[0] ?? null;
                    if (1 !== count($configuration) || !$modifier instanceof TextModifier) {
                        return false;
                    }

                    $reflection = new ReflectionProperty($modifier, 'configuration');
                    $modifierConfiguration = $reflection->getValue($modifier);

                    return 'TEST 1' === $modifierConfiguration['text']
                        && '#111111' === $modifierConfiguration['color'];
                }),
            );

        $resolver = new IndicatorResolver($storage, $this->createStub(TriggerEvaluator::class), new NullLogger(), $this->createInstanceConfiguration(['label' => 'TEST 1']));
        $resolver->resolveIndicators();
    }

    public function testInstanceOverrideDoesNotActivateIndicators(): void
    {
        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('hasCurrentIndicators')->willReturn(false);
        $storage->method('getConfigurations')->willReturn([]);
        $storage->method('getCurrentIndicators')->willReturn([]);
        $storage->expects(self::never())->method('mergeCurrentIndicator');
        $storage->expects(self::never())->method('setCurrentIndicator');

        $resolver = new IndicatorResolver($storage, $this->createStub(TriggerEvaluator::class), new NullLogger(), $this->createInstanceConfiguration(['label' => 'TEST 1', 'color' => '#ff0000']));

        self::assertSame([], $resolver->resolveIndicators());
    }

    public function testInvalidInstanceColorIsIgnoredAndLogged(): void
    {
        $storage = $this->createMock(ConfigurationStorage::class);
        $storage->method('hasCurrentIndicators')->willReturn(false);
        $storage->method('getConfigurations')->willReturn([]);
        $storage->method('getCurrentIndicators')->willReturn([Backend\Toolbar::class => ['color' => '#111111']]);
        $storage->expects(self::never())->method('mergeCurrentIndicator');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('warning');

        $resolver = new IndicatorResolver($storage, $this->createStub(TriggerEvaluator::class), $logger, $this->createInstanceConfiguration(['color' => 'red; injection']));
        $resolver->resolveIndicators();
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

    /**
     * @param array<string, string> $instance
     */
    private function createInstanceConfiguration(array $instance): ExtensionConfiguration
    {
        $extensionConfiguration = $this->createStub(ExtensionConfiguration::class);
        $extensionConfiguration->method('get')
            ->willReturnCallback(static fn (string $extension) => Configuration::EXT_KEY === $extension ? ['instance' => $instance] : null);

        return $extensionConfiguration;
    }
}
