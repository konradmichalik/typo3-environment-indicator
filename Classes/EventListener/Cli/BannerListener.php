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
use Symfony\Component\Console\Output\{ConsoleOutputInterface, OutputInterface, StreamOutput};
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;

use function fnmatch;
use function function_exists;
use function is_array;
use function mb_strwidth;
use function sprintf;
use function stream_isatty;
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

        $errorOutput = $this->resolveErrorOutput($event->getOutput());

        // Only interactive terminals — keeps cron jobs, CI pipelines and
        // scheduler runs (and --no-interaction) free of banner noise.
        // isInteractive() alone can be overridden by extensions/tests without
        // a real TTY, so the output stream is checked as well.
        if (!$event->getInput()->isInteractive() || !$this->isRealTerminal($errorOutput)) {
            return;
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[Banner::class];

        if (!$this->commandMatches($event->getCommand()?->getName(), $configuration['commands'] ?? [])) {
            return;
        }

        $errorOutput->writeln($this->buildBanner($configuration));
    }

    private function isRealTerminal(OutputInterface $output): bool
    {
        if (!$output instanceof StreamOutput) {
            return true;
        }

        return !function_exists('stream_isatty') || stream_isatty($output->getStream());
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

        $main = OutputFormatter::escape('' !== $icon ? $icon.' '.$text : $text);
        $siteLabel = '' !== $sitename ? ' — '.OutputFormatter::escape($sitename) : '';

        if ('' === $color) {
            return $main.$siteLabel;
        }

        // mb_strwidth (not mb_strlen) so double-width glyphs (e.g. the emoji icon)
        // don't leave the padding lines shorter than the content line.
        $paddingLine = str_repeat(' ', mb_strwidth('  '.$main.$siteLabel.'  '));

        // Sibling tags, not nested ones: Symfony's formatter does not inherit
        // the background from an enclosing tag, so each segment declares its
        // own "bg" to keep the bar filled behind the (non-bold) padding. Only
        // the environment label is bold, the site name stays regular weight.
        $bar = sprintf('<bg=%s>', $color);
        $barBold = sprintf('<bg=%s;fg=white;options=bold>', $color);
        $barRegular = sprintf('<bg=%s;fg=white>', $color);

        return "{$bar}{$paddingLine}</>\n"
            ."{$bar}  </>{$barBold}{$main}</>{$barRegular}{$siteLabel}</>{$bar}  </>\n"
            ."{$bar}{$paddingLine}</>";
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
