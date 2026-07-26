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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\EventListener\Mail;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Mail\SubjectPrefix;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * SubjectPrefixListenerTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class SubjectPrefixListenerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]['mail']['subject'] = '1';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['current'] = [
            SubjectPrefix::class => ['prefix' => '[%context%] ', 'header' => 'X-Environment'],
        ];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['resolved'] = true;
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
        );
        parent::tearDown();
    }

    public function testSubjectIsPrefixedWithApplicationContext(): void
    {
        $email = (new Email())->subject('Your registration');

        $this->dispatch($email);

        self::assertSame('[Testing] Your registration', $email->getSubject());
    }

    public function testEnvironmentHeaderIsAdded(): void
    {
        $email = (new Email())->subject('Report');

        $this->dispatch($email);

        self::assertTrue($email->getHeaders()->has('X-Environment'));
        self::assertSame('Testing', $email->getHeaders()->get('X-Environment')?->getBodyAsString());
    }

    public function testSubjectIsNotPrefixedTwice(): void
    {
        $email = (new Email())->subject('Newsletter');

        $this->dispatch($email);
        $this->dispatch($email);

        self::assertSame('[Testing] Newsletter', $email->getSubject());
    }

    private function dispatch(Email $email): void
    {
        $event = new BeforeMailerSentMessageEvent($this->createStub(MailerInterface::class), $email);
        $this->get(EventDispatcherInterface::class)->dispatch($event);
    }
}
