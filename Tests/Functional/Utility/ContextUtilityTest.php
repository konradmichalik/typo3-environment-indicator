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

namespace KonradMichalik\Typo3EnvironmentIndicator\Tests\Functional\Utility;

use KonradMichalik\Ttt\Traits\ConfVarsSandbox;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\ContextUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * ContextUtilityTest.
 *
 * Uses the imperative ConfVarsSandbox trait rather than the
 * WithTypo3ConfVars attribute: the attribute applies before setUp() runs,
 * but FunctionalTestCase::setUp() reloads $GLOBALS['TYPO3_CONF_VARS'] from
 * the real bootstrapped configuration afterwards, discarding it.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ContextUtilityTest extends FunctionalTestCase
{
    use ConfVarsSandbox;

    protected array $testExtensionsToLoad = [
        'typo3_environment_indicator',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->setTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['current' => []]]]);

        $this->importCSVDataSet(__DIR__.'/Fixtures/Pages.csv');

        $this->writeSiteConfigurationYaml('test-site', 1, 'Test Site');
        $this->writeSiteConfigurationYaml('no-title-site', 2, null);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
        $this->restoreTypo3ConfVars();
    }

    public function testGetContextReturnsApplicationContextString(): void
    {
        $contextUtility = new ContextUtility();
        $context = $contextUtility->getContext();

        self::assertIsString($context);
        self::assertNotEmpty($context);
    }

    public function testGetTitleReturnsWebsiteTitleFromSiteConfiguration(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->buildRequestWithPageId(1);

        $contextUtility = new ContextUtility($this->get(SiteFinder::class));

        self::assertSame('Test Site', $contextUtility->getTitle());
    }

    public function testGetTitleFallsBackToSiteIdentifierWhenWebsiteTitleMissing(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->buildRequestWithPageId(2);

        $contextUtility = new ContextUtility($this->get(SiteFinder::class));

        self::assertSame('no-title-site', $contextUtility->getTitle());
    }

    public function testGetTitleReturnsEmptyStringWhenSiteIsNotFound(): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $this->buildRequestWithPageId(999);

        $contextUtility = new ContextUtility($this->get(SiteFinder::class));

        self::assertSame('', $contextUtility->getTitle());
    }

    private function buildRequestWithPageId(int $pageId): ServerRequest
    {
        return (new ServerRequest('https://example.com/', 'GET'))
            ->withAttribute('routing', new PageArguments($pageId, '0', []));
    }

    private function writeSiteConfigurationYaml(string $identifier, int $rootPageId, ?string $websiteTitle): void
    {
        $configDir = Environment::getConfigPath().'/sites/'.$identifier;
        @mkdir($configDir, 0777, true);

        $yaml = "rootPageId: {$rootPageId}\n";
        $yaml .= "base: /\n";

        if (null !== $websiteTitle) {
            $yaml .= "websiteTitle: '{$websiteTitle}'\n";
        }

        $yaml .= "languages:\n";
        $yaml .= "  - title: English\n";
        $yaml .= "    enabled: true\n";
        $yaml .= "    languageId: 0\n";
        $yaml .= "    base: /\n";
        $yaml .= "    locale: en_US.UTF-8\n";

        file_put_contents($configDir.'/config.yaml', $yaml);
    }
}
