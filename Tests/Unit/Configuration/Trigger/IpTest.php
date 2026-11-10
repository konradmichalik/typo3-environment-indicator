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
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Ip;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * IpTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class IpTest extends TestCase
{
    private string $originalRemoteAddr;

    protected function setUp(): void
    {
        // Store original value and set test value
        // @phpstan-ignore-next-line disallowed.variable
        $this->originalRemoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';
    }

    protected function tearDown(): void
    {
        // Restore original value
        if ('' !== $this->originalRemoteAddr) {
            // @phpstan-ignore-next-line disallowed.variable
            $_SERVER['REMOTE_ADDR'] = $this->originalRemoteAddr;
        } else {
            // @phpstan-ignore-next-line disallowed.variable
            unset($_SERVER['REMOTE_ADDR']);
        }
    }

    public function testCheckReturnsTrueForMatchingIp(): void
    {
        $trigger = new Ip('192.168.1.1');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForNonMatchingIp(): void
    {
        $trigger = new Ip('192.168.1.2');
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueForMatchingIpInMultipleIps(): void
    {
        $trigger = new Ip('192.168.1.2', '192.168.1.1', '192.168.1.3');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForNonMatchingIpInMultipleIps(): void
    {
        $trigger = new Ip('192.168.1.2', '192.168.1.3', '192.168.1.4');
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueForMatchingCidrRange(): void
    {
        $trigger = new Ip('192.168.1.0/24');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForNonMatchingCidrRange(): void
    {
        $trigger = new Ip('192.168.2.0/24');
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueForMatchingCidrRangeWith16BitMask(): void
    {
        $trigger = new Ip('192.168.0.0/16');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForNonMatchingCidrRangeWith16BitMask(): void
    {
        $trigger = new Ip('192.169.0.0/16');
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckReturnsTrueForMatchingCidrRangeWith8BitMask(): void
    {
        $trigger = new Ip('192.0.0.0/8');
        $result = $trigger->check();
        self::assertTrue($result);
    }

    public function testCheckReturnsFalseForNonMatchingCidrRangeWith8BitMask(): void
    {
        $trigger = new Ip('10.0.0.0/8');
        $result = $trigger->check();
        self::assertFalse($result);
    }

    public function testCheckWithIPv6Address(): void
    {
        // Temporarily set IPv6 address for testing
        // @phpstan-ignore-next-line disallowed.variable
        $originalAddr = $_SERVER['REMOTE_ADDR'];
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';

        $trigger = new Ip('2001:db8::1');
        $result = $trigger->check();
        self::assertTrue($result);

        // Restore original address
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = $originalAddr;
    }

    public function testCheckWithIPv6CidrRange(): void
    {
        // Temporarily set IPv6 address for testing
        // @phpstan-ignore-next-line disallowed.variable
        $originalAddr = $_SERVER['REMOTE_ADDR'];
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';

        $trigger = new Ip('2001:db8::/32');
        $result = $trigger->check();
        self::assertTrue($result);

        // Restore original address
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = $originalAddr;
    }

    public function testCheckWithIPv6CidrRangeAndNonByteAlignedMask(): void
    {
        // @phpstan-ignore-next-line disallowed.variable
        $originalAddr = $_SERVER['REMOTE_ADDR'];
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = '2001:db8::1';

        $trigger = new Ip('2001:db8::/36');
        self::assertTrue($trigger->check());

        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = $originalAddr;
    }

    public function testConstructorThrowsForInvalidIp(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1726357768);

        new Ip('not-an-ip');
    }

    public function testConstructorThrowsForCidrWithInvalidMask(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ip('192.168.1.0/33');
    }

    public function testConstructorThrowsForCidrWithTooManyParts(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ip('192.168.1.0/24/8');
    }

    public function testCheckReturnsFalseWhenRemoteAddrIsInvalid(): void
    {
        // @phpstan-ignore-next-line disallowed.variable
        $_SERVER['REMOTE_ADDR'] = 'invalid';

        $trigger = new Ip('192.168.1.1');
        self::assertFalse($trigger->check());
    }

    /**
     * @return iterable<string, array{string, string, bool}>
     */
    public static function cidrMatchProvider(): iterable
    {
        yield 'ipv4 mask 0 matches any address' => ['10.20.30.40', '192.168.1.1/0', true];
        yield 'ipv4 mask 32 exact match' => ['192.168.1.1', '192.168.1.1/32', true];
        yield 'ipv4 mask 32 differs in last bit' => ['192.168.1.1', '192.168.1.0/32', false];
        yield 'ipv4 mask 31 matches neighbour' => ['192.168.1.1', '192.168.1.0/31', true];
        yield 'ipv4 mask 24 outside subnet' => ['192.168.2.1', '192.168.1.0/24', false];
        yield 'ipv4 negative mask' => ['10.0.0.1', '10.0.0.1/-1', false];
        yield 'ipv4 mask above 32' => ['10.0.0.1', '10.0.0.1/33', false];
        yield 'ipv4 invalid subnet' => ['10.0.0.1', 'foo/8', false];
        yield 'ipv6 mask 0 matches any address' => ['2001:db8::1', 'fe80::/0', true];
        yield 'ipv6 mask 128 exact match' => ['2001:db8::1', '2001:db8::1/128', true];
        yield 'ipv6 mask 128 differs in last bit' => ['2001:db8::1', '2001:db8::/128', false];
        yield 'ipv6 mask 127 matches neighbour' => ['2001:db8::1', '2001:db8::/127', true];
        yield 'ipv6 mask 127 differs above last bit' => ['2001:db8::2', '2001:db8::/127', false];
        yield 'ipv6 mask 8 byte aligned match' => ['2001:db8::1', '2001:ffff::/8', true];
        yield 'ipv6 mask 8 byte aligned mismatch' => ['2001:db8::1', '2101::/8', false];
        yield 'ipv6 mask 16 byte aligned mismatch' => ['2001:db8::1', '2002::/16', false];
        yield 'ipv6 mask 1 partial byte match' => ['8000::1', 'ffff::/1', true];
        yield 'ipv6 mask 1 partial byte mismatch' => ['8000::1', '7fff::/1', false];
        yield 'ipv6 mask 12 partial byte mismatch' => ['2001:db8::1', '2011::/12', false];
        yield 'ipv6 mask 12 partial byte match' => ['2001:db8::1', '2001:0fff::/12', true];
        yield 'ipv6 mask 120 ignores last byte' => ['2001:db8::1', '2001:db8::ff/120', true];
        yield 'ipv6 mask 120 mismatch in byte 15' => ['2001:db8::100', '2001:db8::/120', false];
        yield 'ipv6 negative mask' => ['2001:db8::1', '2001:db8::1/-1', false];
        yield 'ipv6 mask above 128' => ['2001:db8::1', '2001:db8::1/129', false];
        yield 'ipv6 invalid subnet' => ['2001:db8::1', 'zz::/16', false];
    }

    #[DataProvider('cidrMatchProvider')]
    public function testCidrMatch(string $ip, string $cidr, bool $expected): void
    {
        $trigger = new class('192.168.1.1') extends Ip {
            public function match(string $ip, string $cidr): bool
            {
                return $this->cidrMatch($ip, $cidr);
            }
        };

        self::assertSame($expected, $trigger->match($ip, $cidr));
    }
}
