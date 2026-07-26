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

namespace KonradMichalik\Typo3EnvironmentIndicator\EventListener\Mail;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Mail\SubjectPrefix;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;

use function str_starts_with;
use function trim;

/**
 * SubjectPrefixListener.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class SubjectPrefixListener
{
    #[AsEventListener(identifier: 'typo3-environment-indicator/mail-subject-prefix')]
    public function __invoke(BeforeMailerSentMessageEvent $event): void
    {
        if (!GeneralHelper::isExtensionFeatureEnabled('mail/subject')) {
            return;
        }

        if (!GeneralHelper::isCurrentIndicator(SubjectPrefix::class)) {
            return;
        }

        $message = $event->getMessage();
        if (!$message instanceof Email) {
            return;
        }

        $configuration = GeneralHelper::getIndicatorConfiguration()[SubjectPrefix::class];
        $context = Environment::getContext()->__toString();

        $this->applySubjectPrefix($message, (string) ($configuration['prefix'] ?? ''), $context);
        $this->applyHeader($message, (string) ($configuration['header'] ?? ''), $context);
    }

    private function applySubjectPrefix(Email $message, string $prefix, string $context): void
    {
        $prefix = str_replace('%context%', $context, $prefix);
        if ('' === $prefix) {
            return;
        }

        $subject = $message->getSubject() ?? '';

        // Idempotency: never prefix twice when a message is re-processed (queue/retry).
        if (str_starts_with($subject, $prefix)) {
            return;
        }

        $message->subject($prefix.$subject);
    }

    private function applyHeader(Email $message, string $headerName, string $context): void
    {
        $headerName = trim($headerName);
        if ('' === $headerName) {
            return;
        }

        $headers = $message->getHeaders();
        if ($headers->has($headerName)) {
            return;
        }

        $headers->addTextHeader($headerName, $context);
    }
}
