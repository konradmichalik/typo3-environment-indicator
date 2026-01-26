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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Service;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;
use Psr\Log\{LoggerInterface, NullLogger};
use Throwable;

/**
 * TriggerEvaluator.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TriggerEvaluator
{
    public function __construct(private readonly LoggerInterface $logger = new NullLogger()) {}

    /**
     * Evaluates all triggers in a configuration.
     *
     * @param TriggerInterface[] $triggers Array of trigger objects
     *
     * @return bool True if all triggers pass, false otherwise
     */
    public function evaluateTriggers(array $triggers): bool
    {
        if ([] === $triggers) {
            return true;
        }

        foreach ($triggers as $trigger) {
            if (!$this->evaluateTrigger($trigger)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates that triggers are properly configured.
     *
     * @param array<int, mixed> $triggers Array of potential trigger objects
     *
     * @return bool True if all triggers are valid, false otherwise
     */
    public function validateTriggers(array $triggers): bool
    {
        foreach ($triggers as $trigger) {
            if (!$trigger instanceof TriggerInterface) {
                return false;
            }
        }

        return true;
    }

    /**
     * Evaluates a single trigger.
     *
     * @param TriggerInterface $trigger The trigger to evaluate
     *
     * @return bool True if the trigger passes, false otherwise
     */
    protected function evaluateTrigger(TriggerInterface $trigger): bool
    {
        try {
            return $trigger->check();
        } catch (Throwable $e) {
            $this->logger->warning('Trigger evaluation failed: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);

            return false;
        }
    }
}
