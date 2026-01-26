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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\Image\Factory;

use InvalidArgumentException;
use KonradMichalik\Typo3EnvironmentIndicator\Image\{BackendLogoHandler, FaviconHandler, FrontendImageHandler};
use KonradMichalik\Typo3EnvironmentIndicator\Image\Factory\{ImageHandlerFactory, ImageHandlerFactoryInterface};
use PHPUnit\Framework\TestCase;

/**
 * ImageHandlerFactoryTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ImageHandlerFactoryTest extends TestCase
{
    private ImageHandlerFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ImageHandlerFactory();
    }

    public function testImplementsInterface(): void
    {
        self::assertInstanceOf(ImageHandlerFactoryInterface::class, $this->factory);
    }

    public function testCreateBackendLogoHandler(): void
    {
        $handler = $this->factory->createBackendLogoHandler();
        self::assertInstanceOf(BackendLogoHandler::class, $handler);
    }

    public function testCreateFaviconHandler(): void
    {
        $handler = $this->factory->createFaviconHandler();
        self::assertInstanceOf(FaviconHandler::class, $handler);
    }

    public function testCreateFrontendImageHandler(): void
    {
        $handler = $this->factory->createFrontendImageHandler();
        self::assertInstanceOf(FrontendImageHandler::class, $handler);
    }

    public function testCreateHandlerWithBackendLogoType(): void
    {
        $handler = $this->factory->createHandler('backend_logo');
        self::assertInstanceOf(BackendLogoHandler::class, $handler);
    }

    public function testCreateHandlerWithFaviconType(): void
    {
        $handler = $this->factory->createHandler('favicon');
        self::assertInstanceOf(FaviconHandler::class, $handler);
    }

    public function testCreateHandlerWithFrontendImageType(): void
    {
        $handler = $this->factory->createHandler('frontend_image');
        self::assertInstanceOf(FrontendImageHandler::class, $handler);
    }

    public function testCreateHandlerWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1726357770);
        $this->expectExceptionMessage('Unsupported handler type: invalid');

        $this->factory->createHandler('invalid');
    }

    public function testGetSupportedHandlerTypes(): void
    {
        $types = $this->factory->getSupportedHandlerTypes();

        self::assertContains('backend_logo', $types);
        self::assertContains('favicon', $types);
        self::assertContains('frontend_image', $types);
        self::assertCount(3, $types);
    }
}
