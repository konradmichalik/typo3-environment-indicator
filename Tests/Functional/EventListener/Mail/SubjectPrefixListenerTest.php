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

use KonradMichalik\Ttt\Attribute\Typo3ConfVarsSentinel;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Mail\SubjectPrefix;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Event\BeforeMailerSentMessageEvent;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Fluid\View\TemplatePaths;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * SubjectPrefixListenerTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class SubjectPrefixListenerTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['mail' => ['subject' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [SubjectPrefix::class => ['prefix' => '[%context%] ', 'header' => 'X-Environment']],
                'resolved' => true,
            ]],
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
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

    /**
     * A retry can re-dispatch the event after another listener has already
     * prepended its own text, pushing our prefix past the start of the
     * subject. The idempotency check must still recognize it there.
     */
    public function testSubjectIsNotPrefixedTwiceWhenAnotherListenerPrependedTextFirst(): void
    {
        $email = (new Email())->subject('[External] [Testing] Invoice');

        $this->dispatch($email);

        self::assertSame('[External] [Testing] Invoice', $email->getSubject());
    }

    /**
     * FluidEmail renders its "Subject" template section lazily on the first
     * getBody() call, overwriting any subject set until then. TYPO3 core
     * mails (e.g. password reset) rely on exactly this, so the listener must
     * force that rendering before it reads/prefixes the subject - otherwise
     * the prefix is silently discarded once the transport calls getBody().
     */
    public function testSubjectFromFluidEmailIsPrefixedAfterLazyRendering(): void
    {
        // Fluid's controller/action-based template resolution differs between
        // typo3fluid versions shipped with TYPO3 13.4 and 14.3, so the fixture
        // is addressed directly instead, bypassing that resolution entirely.
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplatePathAndFilename(__DIR__.'/Fixtures/Templates/Mail/LazySubject.fluid.html');

        $email = new FluidEmail($templatePaths);

        $this->dispatch($email);

        self::assertSame('[Testing] Lazily rendered subject', $email->getSubject());
    }

    public function testSubjectAndHeaderAreUntouchedWhenNotConfigured(): void
    {
        $this->setTypo3ConfVars([
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [SubjectPrefix::class => Typo3ConfVarsSentinel::Unset],
                'resolved' => true,
            ]],
        ]);
        $this->setTypo3ConfVars([
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [SubjectPrefix::class => []],
            ]],
        ]);

        $email = (new Email())->subject('Hello');

        $this->dispatch($email);

        self::assertSame('Hello', $email->getSubject());
        self::assertFalse($email->getHeaders()->has('X-Environment'));
    }

    private function dispatch(Email $email): void
    {
        $event = new BeforeMailerSentMessageEvent($this->createStub(MailerInterface::class), $email);
        $this->get(EventDispatcherInterface::class)->dispatch($event);
    }
}
