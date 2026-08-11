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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\ViewHelpers;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Image;
use KonradMichalik\Typo3EnvironmentIndicator\ViewHelpers\ImageViewHelper;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ImageViewHelperTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageViewHelperTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    private const IMAGE_PATH = 'EXT:typo3_environment_indicator/Resources/Public/Icons/Extension.png';

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testRenderReturnsUnmodifiedImageWhenFeatureDisabled(): void
    {
        self::assertSame(self::IMAGE_PATH, $this->createSubject()->render());
    }

    public function testRenderReturnsUnmodifiedImageWhenNoModifiersConfigured(): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['frontend' => ['image' => '1']]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Image::class => []],
                'resolved' => true,
            ]],
        ]);

        self::assertSame(self::IMAGE_PATH, $this->createSubject()->render());
    }

    private function createSubject(): ImageViewHelper
    {
        $viewHelper = new ImageViewHelper($this->get(ExtensionConfiguration::class));
        $viewHelper->setRenderChildrenClosure(static fn () => self::IMAGE_PATH);

        return $viewHelper;
    }
}
