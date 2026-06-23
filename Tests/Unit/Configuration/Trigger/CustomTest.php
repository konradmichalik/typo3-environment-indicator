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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Trigger;

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Custom;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * CustomTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class CustomTest extends TestCase
{
    public function testConstructorAcceptsClosure(): void
    {
        $closure = static fn () => true;
        $trigger = new Custom($closure);
        self::assertInstanceOf(Custom::class, $trigger);
    }

    public function testConstructorAcceptsStaticMethodString(): void
    {
        $staticMethod = self::class.'::staticTestMethod';
        $trigger = new Custom($staticMethod);
        self::assertInstanceOf(Custom::class, $trigger);
    }

    public function testConstructorThrowsExceptionForInvalidFunction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1726357767);
        $this->expectExceptionMessage('Function must be a callable or a valid static method string.');

        new Custom('invalidFunction');
    }

    public function testCheckReturnsTrueForTrueClosure(): void
    {
        $closure = static fn () => true;
        $trigger = new Custom($closure);
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForFalseClosure(): void
    {
        $closure = static fn () => false;
        $trigger = new Custom($closure);
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckCallsStaticMethod(): void
    {
        $trigger = new Custom(self::class.'::staticTestMethod');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testConstructorThrowsForInvalidClassName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid class name format');

        new Custom('123InvalidClass::method');
    }

    public function testConstructorThrowsForInvalidMethodName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid method name format');

        new Custom('ValidClass::123invalidMethod');
    }

    public function testConstructorThrowsForNonExistentClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class or method does not exist');

        new Custom('NonExistentClassXyz123::someMethod');
    }

    public function testConstructorThrowsForNonStaticMethod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method must be public and static');

        new Custom(self::class.'::nonStaticTestMethod');
    }

    public function testCheckReturnsFalseWhenClosureThrowsException(): void
    {
        $closure = static fn () => throw new RuntimeException('Test exception');
        $trigger = new Custom($closure);

        self::assertFalse($trigger->check());
    }

    public static function staticTestMethod(): bool
    {
        return true;
    }

    public function nonStaticTestMethod(): bool
    {
        return true;
    }
}
