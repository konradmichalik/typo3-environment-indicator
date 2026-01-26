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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Utility;

use KonradMichalik\Typo3EnvironmentIndicator\Utility\ViewFactoryHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * ViewFactoryHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ViewFactoryHelperTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS'] = [
            'SYS' => [
                'Objects' => [],
            ],
        ];
    }

    public function testRenderViewMethodIsStatic(): void
    {
        $reflection = new ReflectionClass(ViewFactoryHelper::class);
        $method = $reflection->getMethod('renderView');
        self::assertTrue($method->isStatic());
    }

    public function testRenderViewParameterSignature(): void
    {
        $reflection = new ReflectionClass(ViewFactoryHelper::class);
        $method = $reflection->getMethod('renderView');
        $parameters = $method->getParameters();

        self::assertCount(3, $parameters);
        self::assertEquals('template', $parameters[0]->getName());
        self::assertEquals('values', $parameters[1]->getName());
        self::assertEquals('request', $parameters[2]->getName());
        self::assertTrue($parameters[2]->allowsNull());
    }
}
