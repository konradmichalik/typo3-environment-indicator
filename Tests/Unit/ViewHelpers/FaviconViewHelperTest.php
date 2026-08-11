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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Unit\ViewHelpers;

use KonradMichalik\Typo3EnvironmentIndicator\ViewHelpers\FaviconViewHelper;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * FaviconViewHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class FaviconViewHelperTest extends TestCase
{
    public function testRenderReturnsChildrenWhenRenderingContextIsNull(): void
    {
        $viewHelper = new FaviconViewHelper($this->createStub(ExtensionConfiguration::class));
        $viewHelper->setRenderChildrenClosure(static fn () => 'typo3conf/ext/foo/favicon.ico');

        self::assertSame('typo3conf/ext/foo/favicon.ico', $viewHelper->render());
    }
}
