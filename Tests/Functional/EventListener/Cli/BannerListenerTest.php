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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\EventListener\Cli;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Cli\Banner;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * BannerListenerTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BannerListenerTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['cli' => ['banner' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Banner::class => ['icon' => '🚦']],
                'resolved' => true,
            ]],
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testBannerFallsBackToApplicationContext(): void
    {
        $output = new BufferedOutput();
        $event = new ConsoleCommandEvent(null, new ArrayInput([]), $output);

        $this->get(EventDispatcherInterface::class)->dispatch($event);

        self::assertStringContainsString('Testing', $output->fetch());
    }
}
