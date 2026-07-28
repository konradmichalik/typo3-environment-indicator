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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\EventListener\Mail;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Mail\SubjectPrefix;
use KonradMichalik\Typo3EnvironmentIndicator\EventListener\Mail\SubjectPrefixListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SubjectPrefixListenerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class SubjectPrefixListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
    }

    public function testSubjectIsUntouchedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $email = (new Email())->subject('Hello');

        (new SubjectPrefixListener())($this->buildEvent($email));

        self::assertSame('Hello', $email->getSubject());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => null]])]
    public function testSubjectIsUntouchedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $email = (new Email())->subject('Hello');

        (new SubjectPrefixListener())($this->buildEvent($email));

        self::assertSame('Hello', $email->getSubject());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => [
        'current' => [SubjectPrefix::class => ['prefix' => '[STG] ']],
        'resolved' => true,
    ]]])]
    public function testNonEmailMessageIsIgnored(): void
    {
        $this->mockExtensionConfiguration(true);
        $message = new \Symfony\Component\Mime\RawMessage('raw');

        $event = new BeforeMailerSentMessageEvent($this->createStub(MailerInterface::class), $message);

        // Must not throw when the message is not a fully featured Email object.
        (new SubjectPrefixListener())($event);

        self::assertSame($message, $event->getMessage());
    }

    private function mockExtensionConfiguration(bool $enabled): void
    {
        $mock = $this->createMock(ExtensionConfiguration::class);
        $mock->method('get')->willReturn($enabled);
        GeneralUtility::addInstance(ExtensionConfiguration::class, $mock);
    }

    private function buildEvent(Email $email): BeforeMailerSentMessageEvent
    {
        return new BeforeMailerSentMessageEvent($this->createStub(MailerInterface::class), $email);
    }
}
