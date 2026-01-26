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

namespace KonradMichalik\Typo3EnvironmentIndicator\TypoScript;

use KonradMichalik\Typo3EnvironmentIndicator\Configuration;
use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Frontend\Hint;
use KonradMichalik\Typo3EnvironmentIndicator\Utility\GeneralHelper;
use Symfony\Component\ExpressionLanguage\{ExpressionFunction, ExpressionFunctionProviderInterface};
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * TechnicalContextConditionFunctionsProvider.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class TechnicalContextConditionFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        protected readonly ExtensionConfiguration $extensionConfiguration,
    ) {}

    public function getFunctions(): array
    {
        return [
            $this->getWebserviceFunction(),
        ];
    }

    protected function getWebserviceFunction(): ExpressionFunction
    {
        $extensionConfiguration = $this->extensionConfiguration;

        return new ExpressionFunction(
            'enableTechnicalContext',
            static fn () => null,
            static function () use ($extensionConfiguration) {
                $extensionConfig = $extensionConfiguration->get(Configuration::EXT_KEY);

                return true === (bool) ($extensionConfig['frontend']['context'] ?? false)
                    && GeneralHelper::isCurrentIndicator(Hint::class);
            },
        );
    }
}
