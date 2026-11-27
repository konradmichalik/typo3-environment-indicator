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

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\{AbstractIndicator, Backend, Cli, Frontend, General, Mail};
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * IndicatorDefaultsTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class IndicatorDefaultsTest extends TestCase
{
    use ConfVarsSandbox;

    protected function tearDown(): void
    {
        $this->restoreTypo3ConfVars();
    }

    /**
     * @return array<string, array{class-string<AbstractIndicator>}>
     */
    public static function indicatorDataProvider(): array
    {
        return [
            'Backend\Login' => [Backend\Login::class],
            'Backend\Logo' => [Backend\Logo::class],
            'Backend\Theme' => [Backend\Theme::class],
            'Backend\Toolbar' => [Backend\Toolbar::class],
            'Backend\Topbar' => [Backend\Topbar::class],
            'Backend\Widget' => [Backend\Widget::class],
            'Cli\Banner' => [Cli\Banner::class],
            'Frontend\Hint' => [Frontend\Hint::class],
            'Frontend\HttpHeader' => [Frontend\HttpHeader::class],
            'Frontend\Image' => [Frontend\Image::class],
            'Frontend\Robots' => [Frontend\Robots::class],
            'General\Console' => [General\Console::class],
            'General\PageTitle' => [General\PageTitle::class],
            'Mail\SubjectPrefix' => [Mail\SubjectPrefix::class],
        ];
    }

    /**
     * @param class-string<AbstractIndicator> $indicatorClass
     */
    #[DataProvider('indicatorDataProvider')]
    public function testMergesDefaultsRegisteredForItsOwnClass(string $indicatorClass): void
    {
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['defaults' => [
            $indicatorClass => ['color' => 'default', 'name' => 'default'],
        ]]]]);

        $indicator = new $indicatorClass(['name' => 'local']);

        self::assertSame(['color' => 'default', 'name' => 'local'], $indicator->getConfiguration());
    }
}
