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

use KonradMichalik\Ttt\Attribute\Typo3ConfVarsSentinel;
use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Favicon;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\TriangleModifier;
use KonradMichalik\Typo3EnvironmentIndicator\ViewHelpers\FaviconViewHelper;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;

/**
 * FaviconViewHelperTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FaviconViewHelperTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    private const FAVICON_PATH = 'EXT:typo3_environment_indicator/Resources/Public/Icons/favicon.ico';

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testRenderReturnsUnmodifiedFaviconWhenFeatureDisabled(): void
    {
        self::assertSame(self::FAVICON_PATH, $this->createSubject()->render());
    }

    public function testRenderReturnsUnmodifiedFaviconWhenNoModifiersConfigured(): void
    {
        // A plain [] override can't clear an already-resolved 'current' entry
        // (merging [] onto an existing array subtree is a no-op), so the prior
        // state is unset first to guarantee Favicon::class truly has no modifiers.
        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => Typo3ConfVarsSentinel::Unset]]]);
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['favicon' => true]]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Favicon::class => []],
                'resolved' => true,
            ]],
        ]);

        self::assertSame(self::FAVICON_PATH, $this->createSubject()->render());
    }

    public function testRenderResolvesAndProcessesFaviconWhenFeatureEnabled(): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['favicon' => true]]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Favicon::class => [new TriangleModifier(['color' => '#ff0000'])]],
                'resolved' => true,
            ]],
        ]);

        $result = $this->createSubject()->render();

        self::assertNotSame(self::FAVICON_PATH, $result);
        self::assertStringEndsWith('.png', $result);
    }

    public function testRenderResolvesFaviconPathWithQueryString(): void
    {
        $this->setTypo3ConfVars([
            'EXTENSIONS' => [Configuration::EXT_KEY => ['backend' => ['favicon' => true]]],
            'EXTCONF' => [Configuration::EXT_KEY => [
                'current' => [Favicon::class => [new TriangleModifier(['color' => '#ff0000'])]],
                'resolved' => true,
            ]],
        ]);

        $relativeFaviconPath = 'typo3conf/ext/typo3_environment_indicator/Resources/Public/Icons/favicon.ico?1234567890';

        $viewHelper = $this->createSubject();
        $viewHelper->setRenderChildrenClosure(static fn () => $relativeFaviconPath);

        $result = $viewHelper->render();

        self::assertSame($relativeFaviconPath, $result, 'original reference must be kept when process() leaves the resolved (query-stripped) path unchanged');
    }

    public function testInitializeArgumentsRegistersFaviconArgument(): void
    {
        $viewHelper = new FaviconViewHelper($this->get(ExtensionConfiguration::class));

        self::assertArrayHasKey('favicon', $viewHelper->prepareArguments());
    }

    private function createSubject(): FaviconViewHelper
    {
        $viewHelper = new FaviconViewHelper($this->get(ExtensionConfiguration::class));
        $viewHelper->setRenderChildrenClosure(static fn () => self::FAVICON_PATH);

        $request = (new ServerRequest())->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $renderingContext = new RenderingContext();
        $renderingContext->setAttribute(ServerRequestInterface::class, $request);
        $viewHelper->setRenderingContext($renderingContext);

        return $viewHelper;
    }
}
