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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

use InvalidArgumentException;

use function chr;
use function count;

/**
 * Ip.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Ip implements TriggerInterface
{
    /**
     * @var array<int, string>
     */
    protected array $ips;

    public function __construct(string ...$ip)
    {
        foreach ($ip as $ipAddress) {
            if (!$this->validateIpFormat($ipAddress)) {
                throw new InvalidArgumentException('Invalid IP address or CIDR format: '.$ipAddress, 1726357768);
            }
        }
        $this->ips = array_values($ip);
    }

    public function check(): bool
    {
        $currentIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if (false === filter_var($currentIp, \FILTER_VALIDATE_IP)) {
            return false;
        }

        foreach ($this->ips as $ip) {
            if ($this->ipMatches($currentIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the current IP matches the given IP or CIDR range.
     *
     * @param string $currentIp the current IP address
     * @param string $ip        the IP address or CIDR range to match against
     *
     * @return bool true if the current IP matches, false otherwise
     */
    protected function ipMatches(string $currentIp, string $ip): bool
    {
        if (str_contains($ip, '/')) {
            return $this->cidrMatch($currentIp, $ip);
        }

        return $currentIp === $ip;
    }

    /**
     * Checks if the current IP is within the given CIDR range.
     *
     * @param string $ip   the current IP address
     * @param string $cidr the CIDR range to match against
     *
     * @return bool true if the current IP is within the CIDR range, false otherwise
     */
    protected function cidrMatch(string $ip, string $cidr): bool
    {
        $parts = explode('/', $cidr);
        if (2 !== count($parts)) {
            return false;
        }

        [$subnet, $mask] = $parts;
        $maskInt = (int) $mask;

        if (false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
            if ($maskInt < 0 || $maskInt > 32) {
                return false;
            }

            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);

            if (false === $ipLong || false === $subnetLong) {
                return false;
            }

            $maskBits = ~((1 << (32 - $maskInt)) - 1);

            return ($ipLong & $maskBits) === ($subnetLong & $maskBits);
        }

        if (false !== filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
            if ($maskInt < 0 || $maskInt > 128) {
                return false;
            }

            $ipBinary = inet_pton($ip);
            $subnetBinary = inet_pton($subnet);

            if (false === $ipBinary || false === $subnetBinary) {
                return false;
            }

            $maskBinary = str_repeat("\xff", (int) ($maskInt / 8));
            if (0 !== $maskInt % 8) {
                $maskBinary .= chr((0xFF << (8 - ($maskInt % 8))) & 0xFF);
            }
            $maskBinary = str_pad($maskBinary, 16, "\x00");

            return substr($ipBinary & $maskBinary, 0, 16) === substr($subnetBinary & $maskBinary, 0, 16);
        }

        return false;
    }

    /**
     * Validates IP address or CIDR format.
     *
     * @param string $ip the IP address or CIDR to validate
     *
     * @return bool true if valid, false otherwise
     */
    protected function validateIpFormat(string $ip): bool
    {
        // Check if it's a CIDR notation
        if (str_contains($ip, '/')) {
            $parts = explode('/', $ip);
            if (2 !== count($parts)) {
                return false;
            }

            [$address, $mask] = $parts;

            // Validate mask range
            $maskInt = (int) $mask;
            if (false !== filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4)) {
                return $maskInt >= 0 && $maskInt <= 32;
            }

            if (false !== filter_var($address, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV6)) {
                return $maskInt >= 0 && $maskInt <= 128;
            }

            return false;
        }

        // Check if it's a valid IP address
        return false !== filter_var($ip, \FILTER_VALIDATE_IP);
    }
}
