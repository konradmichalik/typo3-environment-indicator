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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Image\Modifier;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TextModifier;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * TextModifierTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class TextModifierTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    public function testModifyRendersTextWithCustomFont(): void
    {
        $fontPath = GeneralUtility::getFileAbsFileName('EXT:core/Resources/Private/Font/nimbus.ttf');
        self::assertFileExists($fontPath);

        $image = (new ImageManager(new Driver()))->create(64, 64);
        $modifier = new TextModifier([
            'text' => 'DEV',
            'color' => '#ffffff',
            'font' => $fontPath,
        ]);

        $modifier->modify($image);

        self::assertSame(64, $image->width());
    }
}
