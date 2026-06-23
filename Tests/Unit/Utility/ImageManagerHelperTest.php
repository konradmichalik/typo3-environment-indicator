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

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ImageManagerHelper;
use PHPUnit\Framework\TestCase;

/**
 * ImageManagerHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageManagerHelperTest extends TestCase
{
    private ImageManager $manager;

    protected function setUp(): void
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function testIsVersion4ReturnsBoolean(): void
    {
        self::assertIsBool(ImageManagerHelper::isVersion4());
    }

    public function testReadImageReturnsImageInterface(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'phpunit_').'.png';
        $this->manager->create(10, 10, 'ffffff')->save($tmpFile);

        $result = ImageManagerHelper::readImage($this->manager, $tmpFile);

        unlink($tmpFile);
        self::assertInstanceOf(ImageInterface::class, $result);
    }

    public function testDrawCircleProducesNoErrors(): void
    {
        $image = $this->manager->create(64, 64, 'ffffff');
        ImageManagerHelper::drawCircle($image, 32, 32, 10, '#ff0000');
        self::assertSame(64, $image->width());
    }

    public function testDrawRectangleProducesNoErrors(): void
    {
        $image = $this->manager->create(64, 64, 'ffffff');
        ImageManagerHelper::drawRectangle($image, 10, 10, 30, 30, '#ff0000', 2);
        self::assertSame(64, $image->width());
    }

    public function testPlaceImageProducesNoErrors(): void
    {
        $image = $this->manager->create(64, 64, 'ffffff');
        $overlay = $this->manager->create(16, 16, 'ff0000');
        ImageManagerHelper::placeImage($image, $overlay, 'top-left', 0, 0);
        self::assertSame(64, $image->width());
    }

    public function testSetFontAlignmentProducesNoErrors(): void
    {
        $image = $this->manager->create(64, 64, 'ffffff');
        $image->text('test', 10, 10, static function ($font): void {
            ImageManagerHelper::setFontAlignment($font, 'left', 'top');
        });
        self::assertSame(64, $image->width());
    }
}
