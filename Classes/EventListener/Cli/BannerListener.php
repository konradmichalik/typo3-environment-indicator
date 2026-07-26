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

namespace KonradMichalik\Typo3EnvironmentIndicator\EventListener\Cli;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Cli\Banner;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\{ConsoleOutputInterface, OutputInterface};
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;

use function fnmatch;
use function is_array;
use function sprintf;
use function trim;

/**
 * BannerListener.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class BannerListener
{
    #[AsEventListener(identifier: 'typo3-environment-indicator/cli-banner')]
    public function __invoke(ConsoleCommandEvent $event): void
    {
        if (!GeneralHelper::isExtensionFeatureEnabled('cli/banner')) {
            return;
        }

        if (!GeneralHelper::isCurrentIndicator(Banner::class)) {
            return;
        }

        // Only interactive terminals — keeps cron jobs, CI pipelines and
        // scheduler runs (and --no-interaction) free of banner noise.
        if (!$event->getInput()->isInteractive()) {
            return;
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[Banner::class];

        if (!$this->commandMatches($event->getCommand()?->getName(), $configuration['commands'] ?? [])) {
            return;
        }

        $this->resolveErrorOutput($event->getOutput())->writeln($this->buildBanner($configuration));
    }

    /**
     * @param array<string|int, mixed> $configuration
     */
    private function buildBanner(array $configuration): string
    {
        $icon = trim((string) ($configuration['icon'] ?? ''));
        $text = (string) ($configuration['text'] ?? Environment::getContext()->__toString());
        $color = trim((string) ($configuration['color'] ?? ''));
        $sitename = trim((string) ($GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] ?? ''));

        $label = '' !== $icon ? $icon.' '.$text : $text;
        if ('' !== $sitename) {
            $label .= ' — '.$sitename;
        }

        $label = OutputFormatter::escape($label);

        // Non-decorated output (piped stdout, NO_COLOR) strips the tags automatically.
        return '' !== $color ? sprintf('<fg=%s;options=bold>%s</>', $color, $label) : $label;
    }

    /**
     * Prints to stderr so parsed stdout output (JSON, CSV) stays untouched.
     */
    private function resolveErrorOutput(OutputInterface $output): OutputInterface
    {
        return $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
    }

    private function commandMatches(?string $commandName, mixed $patterns): bool
    {
        if (!is_array($patterns) || [] === $patterns) {
            return true;
        }

        if (null === $commandName) {
            return false;
        }

        foreach ($patterns as $pattern) {
            if (fnmatch((string) $pattern, $commandName)) {
                return true;
            }
        }

        return false;
    }
}
