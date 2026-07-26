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
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        GeneralUtility::purgeInstances();
    }

    public function testSubjectIsUntouchedWhenFeatureDisabled(): void
    {
        $this->mockExtensionConfiguration(false);
        $email = (new Email())->subject('Hello');

        (new SubjectPrefixListener())($this->buildEvent($email));

        self::assertSame('Hello', $email->getSubject());
    }

    public function testSubjectIsUntouchedWhenIndicatorNotResolved(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([]);
        $email = (new Email())->subject('Hello');

        (new SubjectPrefixListener())($this->buildEvent($email));

        self::assertSame('Hello', $email->getSubject());
    }

    public function testNonEmailMessageIsIgnored(): void
    {
        $this->mockExtensionConfiguration(true);
        $this->setResolvedIndicators([SubjectPrefix::class => ['prefix' => '[STG] ']]);
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

    /**
     * @param array<class-string, array<string, mixed>> $indicators
     */
    private function setResolvedIndicators(array $indicators): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = $indicators;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    private function buildEvent(Email $email): BeforeMailerSentMessageEvent
    {
        return new BeforeMailerSentMessageEvent($this->createStub(MailerInterface::class), $email);
    }
}
