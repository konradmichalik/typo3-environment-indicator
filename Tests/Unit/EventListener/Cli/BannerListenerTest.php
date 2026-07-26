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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\EventListener\Cli;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Cli\Banner;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Cli\BannerListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\{ArrayInput, InputInterface};
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BannerListenerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class BannerListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        GeneralUtility::purgeInstances();
    }

    public function testNothingIsPrintedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        self::assertSame('', $output->fetch());
    }

    public function testNothingIsPrintedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([]);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        self::assertSame('', $output->fetch());
    }

    public function testNothingIsPrintedForNonInteractiveInput(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([Banner::class => ['text' => 'STAGING']]);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output, $this->input(false)));

        self::assertSame('', $output->fetch());
    }

    public function testBannerIsPrintedWithTextAndSiteName(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'My Project';
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([Banner::class => ['text' => 'STAGING', 'icon' => '🚦', 'color' => 'cyan']]);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        $printed = $output->fetch();
        self::assertStringContainsString('🚦 STAGING', $printed);
        self::assertStringContainsString('My Project', $printed);

        unset($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename']);
    }

    public function testBannerIsNotPrintedWhenCommandDoesNotMatchWhitelist(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([Banner::class => ['text' => 'STAGING', 'commands' => ['cache:*']]]);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output, command: new Command('site:list')));

        self::assertSame('', $output->fetch());
    }

    public function testBannerIsPrintedWhenCommandMatchesWhitelist(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([Banner::class => ['text' => 'STAGING', 'commands' => ['cache:*']]]);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output, command: new Command('cache:flush')));

        self::assertStringContainsString('STAGING', $output->fetch());
    }

    private function mockExtensionConfiguration(bool $enabled): void
    {
        $mock = $this->createMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturn($enabled);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $mock);
    }

    /**
     * @param array<class-string, array<string, mixed>> $indicators
     */
    private function setResolvedIndicators(array $indicators): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = $indicators;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    private function input(bool $interactive): InputInterface
    {
        $input = new ArrayInput([]);
        $input->setInteractive($interactive);

        return $input;
    }

    private function buildEvent(BufferedOutput $output, ?InputInterface $input = null, ?Command $command = null): ConsoleCommandEvent
    {
        return new ConsoleCommandEvent($command, $input ?? $this->input(true), $output);
    }
}
