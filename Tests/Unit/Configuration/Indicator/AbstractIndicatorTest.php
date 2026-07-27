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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Configuration\Indicator;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, IndicatorInterface};
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AbstractIndicatorTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['defaults' => null]]])]
class AbstractIndicatorTest extends TestCase
{
    public function testConstructorWithEmptyConfiguration(): void
    {
        $indicator = new ConcreteIndicator();
        self::assertEquals([], $indicator->getConfiguration());
    }

    public function testConstructorWithConfiguration(): void
    {
        $config = ['key' => 'value'];
        $indicator = new ConcreteIndicator($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testConstructorWithRequest(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $config = ['key' => 'value'];
        $indicator = new ConcreteIndicator($config, $request);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    public function testMergeGlobalConfigurationWithNoGlobal(): void
    {
        $config = ['key' => 'value'];
        $indicator = new ConcreteIndicator($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['defaults' => [
        ConcreteIndicator::class => ['global' => 'value', 'override' => 'global'],
    ]]]])]
    public function testMergeGlobalConfigurationWithGlobal(): void
    {
        $localConfig = ['local' => 'value', 'override' => 'local'];
        $indicator = new ConcreteIndicator($localConfig);

        $expected = [
            'global' => 'value',
            'local' => 'value',
            'override' => 'local',
        ];
        self::assertEquals($expected, $indicator->getConfiguration());
    }

    public function testMergeGlobalConfigurationWithEmptyGlobal(): void
    {
        $config = ['key' => 'value'];
        $indicator = new ConcreteIndicator($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }

    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['defaults' => 'not an array']]])]
    public function testMergeGlobalConfigurationWithNonArrayGlobal(): void
    {
        $config = ['key' => 'value'];
        $indicator = new ConcreteIndicator($config);
        self::assertEquals($config, $indicator->getConfiguration());
    }
}

/**
 * ConcreteIndicator.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConcreteIndicator extends AbstractIndicator implements IndicatorInterface
{
    /**
     * @return array<string|int, mixed|ModifierInterface>
     */
    public function getConfiguration(): array
    {
        return parent::getConfiguration();
    }
}
