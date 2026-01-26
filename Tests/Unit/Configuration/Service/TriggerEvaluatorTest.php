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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service\TriggerEvaluator;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use stdClass;

/**
 * TriggerEvaluatorTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TriggerEvaluatorTest extends TestCase
{
    private TriggerEvaluator $triggerEvaluator;

    protected function setUp(): void
    {
        $this->triggerEvaluator = new TriggerEvaluator();
    }

    public function testEvaluateTriggersReturnsTrueForEmptyArray(): void
    {
        self::assertTrue($this->triggerEvaluator->evaluateTriggers([]));
    }

    public function testEvaluateTriggersReturnsTrueWhenAllTriggersPass(): void
    {
        $trigger1 = $this->createMock(TriggerInterface::class);
        $trigger1->expects(self::once())->method('check')->willReturn(true);

        $trigger2 = $this->createMock(TriggerInterface::class);
        $trigger2->expects(self::once())->method('check')->willReturn(true);

        $result = $this->triggerEvaluator->evaluateTriggers([$trigger1, $trigger2]);
        self::assertTrue($result);
    }

    public function testEvaluateTriggersReturnsFalseWhenOneTriggerFails(): void
    {
        $trigger1 = $this->createMock(TriggerInterface::class);
        $trigger1->expects(self::once())->method('check')->willReturn(true);

        $trigger2 = $this->createMock(TriggerInterface::class);
        $trigger2->expects(self::once())->method('check')->willReturn(false);

        $result = $this->triggerEvaluator->evaluateTriggers([$trigger1, $trigger2]);
        self::assertFalse($result);
    }

    public function testEvaluateTriggersHandlesException(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('warning')
            ->with(
                'Trigger evaluation failed: {message}',
                self::callback(static fn (array $context): bool => 'Test exception' === $context['message'] && $context['exception'] instanceof Exception),
            );

        $triggerEvaluator = new TriggerEvaluator($logger);

        $trigger = $this->createMock(TriggerInterface::class);
        $trigger->expects(self::once())->method('check')->willThrowException(new Exception('Test exception'));

        $result = $triggerEvaluator->evaluateTriggers([$trigger]);
        self::assertFalse($result);
    }

    public function testValidateTriggersReturnsTrueForValidTriggers(): void
    {
        $trigger1 = $this->createStub(TriggerInterface::class);
        $trigger2 = $this->createStub(TriggerInterface::class);

        $result = $this->triggerEvaluator->validateTriggers([$trigger1, $trigger2]);
        self::assertTrue($result);
    }

    public function testValidateTriggersReturnsFalseForInvalidTriggers(): void
    {
        $trigger = $this->createStub(TriggerInterface::class);
        $invalidTrigger = new stdClass();

        $result = $this->triggerEvaluator->validateTriggers([$trigger, $invalidTrigger]);
        self::assertFalse($result);
    }

    public function testValidateTriggersReturnsTrueForEmptyArray(): void
    {
        $result = $this->triggerEvaluator->validateTriggers([]);
        self::assertTrue($result);
    }
}
