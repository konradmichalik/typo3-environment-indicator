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

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
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
        GeneralUtility::purgeInstances();
    }

    public function testNothingIsPrintedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        self::assertSame('', $output->fetch());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => [], 'resolved' => true]]])]
    public function testNothingIsPrintedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        self::assertSame('', $output->fetch());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Banner::class => ['text' => 'STAGING']],
        'resolved' => true,
    ]]])]
    public function testNothingIsPrintedForNonInteractiveInput(): void
    {
        $this->mockExtensionConfiguration(true);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output, $this->input(false)));

        self::assertSame('', $output->fetch());
    }

    #[WithTypo3ConfVars([
        'EXTCONF' => [Configuration::EXT_KEY => [
            'current' => [Banner::class => ['text' => 'STAGING', 'icon' => '🚦', 'color' => 'cyan']],
            'resolved' => true,
        ]],
        'SYS' => ['sitename' => 'My Project'],
    ])]
    public function testBannerIsPrintedWithTextAndSiteName(): void
    {
        $this->mockExtensionConfiguration(true);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output));

        $printed = $output->fetch();
        self::assertStringContainsString('🚦 STAGING', $printed);
        self::assertStringContainsString('My Project', $printed);
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Banner::class => ['text' => 'STAGING', 'commands' => ['cache:*']]],
        'resolved' => true,
    ]]])]
    public function testBannerIsNotPrintedWhenCommandDoesNotMatchWhitelist(): void
    {
        $this->mockExtensionConfiguration(true);
        $output = new BufferedOutput();

        (new BannerListener())($this->buildEvent($output, command: new Command('site:list')));

        self::assertSame('', $output->fetch());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [Banner::class => ['text' => 'STAGING', 'commands' => ['cache:*']]],
        'resolved' => true,
    ]]])]
    public function testBannerIsPrintedWhenCommandMatchesWhitelist(): void
    {
        $this->mockExtensionConfiguration(true);
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
