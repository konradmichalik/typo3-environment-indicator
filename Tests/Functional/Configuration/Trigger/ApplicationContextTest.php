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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Configuration\Trigger;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ApplicationContextTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ApplicationContextTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    public function testCheckReturnsTrueForWildcardPattern(): void
    {
        $trigger = new ApplicationContext('*');
        self::assertTrue($trigger->check());
    }

    public function testCheckReturnsTrueWhenCurrentContextMatches(): void
    {
        $currentContext = \TYPO3\CMS\Core\Core\Environment::getContext()->__toString();
        $trigger = new ApplicationContext($currentContext);
        self::assertTrue($trigger->check());
    }

    public function testCheckReturnsFalseForNonMatchingContext(): void
    {
        $trigger = new ApplicationContext('Staging/SubContext12345Unlikely');
        self::assertFalse($trigger->check());
    }

    public function testCheckReturnsTrueForMultipleContextsWhenOneMatches(): void
    {
        $currentContext = \TYPO3\CMS\Core\Core\Environment::getContext()->__toString();
        $trigger = new ApplicationContext('Staging/SubContext12345Unlikely', $currentContext);
        self::assertTrue($trigger->check());
    }

    public function testCheckReturnsFalseWhenNoContextMatches(): void
    {
        $trigger = new ApplicationContext('Context/A', 'Context/B', 'Context/C');
        self::assertFalse($trigger->check());
    }
}
