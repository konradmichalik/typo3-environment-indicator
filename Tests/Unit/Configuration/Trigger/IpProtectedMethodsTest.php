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

use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Ip;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * IpProtectedMethodsTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class IpProtectedMethodsTest extends TestCase
{
    private Ip $trigger;
    private ReflectionMethod $cidrMatch;
    private ReflectionMethod $validateIpFormat;

    protected function setUp(): void
    {
        $this->trigger = new Ip('192.168.1.1');
        $this->cidrMatch = new ReflectionMethod(Ip::class, 'cidrMatch');
        $this->validateIpFormat = new ReflectionMethod(Ip::class, 'validateIpFormat');
    }

    public function testCidrMatchReturnsFalseForCidrWithoutSlash(): void
    {
        self::assertFalse($this->cidrMatch->invoke($this->trigger, '192.168.1.1', 'not-a-cidr'));
    }

    public function testCidrMatchReturnsFalseForIpv4MaskOutOfRange(): void
    {
        self::assertFalse($this->cidrMatch->invoke($this->trigger, '192.168.1.1', '192.168.1.0/33'));
    }

    public function testCidrMatchReturnsFalseForIpv4InvalidSubnet(): void
    {
        self::assertFalse($this->cidrMatch->invoke($this->trigger, '192.168.1.1', '999.999.999.0/24'));
    }

    public function testCidrMatchReturnsFalseForIpv6MaskOutOfRange(): void
    {
        self::assertFalse($this->cidrMatch->invoke($this->trigger, '2001:db8::1', '2001:db8::/256'));
    }

    public function testCidrMatchReturnsFalseForNeitherIpv4NorIpv6(): void
    {
        self::assertFalse($this->cidrMatch->invoke($this->trigger, 'not-an-ip', '192.168.1.0/24'));
    }

    public function testValidateIpFormatReturnsFalseForCidrWithInvalidAddress(): void
    {
        self::assertFalse($this->validateIpFormat->invoke($this->trigger, 'foo.bar/24'));
    }

    public function testCidrMatchReturnsFalseForIpv6WithInvalidSubnet(): void
    {
        // Client IP is valid IPv6, but subnet part is not valid IPv6 → inet_pton returns false
        self::assertFalse($this->cidrMatch->invoke($this->trigger, '::1', 'garbage::/16'));
    }
}
