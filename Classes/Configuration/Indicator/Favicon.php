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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;

use KonradMichalik\Typo3EnvironmentIndicator\Enum\Scope;
use KonradMichalik\Typo3EnvironmentIndicator\Image\Modifier\ModifierInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * Favicon.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Favicon extends AbstractIndicator implements IndicatorInterface
{
    /**
     * @param array<string|int, mixed|ModifierInterface> $configuration
     */
    public function __construct(
        protected array $configuration = [],
        protected Scope $scope = Scope::Global,
        ?ServerRequestInterface $request = null,
    ) {
        parent::__construct($this->configuration, $request);
    }

    /**
     * @return array<string|int, mixed|ModifierInterface>
     */
    public function getConfiguration(): array
    {
        $request = $this->getRequest();
        $applicationType = null !== $request ? ApplicationType::fromRequest($request) : null;

        switch ($this->scope) {
            case Scope::Global:
                return $this->configuration;
            case Scope::Backend:
                if (null !== $applicationType && $applicationType->isBackend()) {
                    return $this->configuration;
                }
                break;
            case Scope::Frontend:
                if (null !== $applicationType && $applicationType->isFrontend()) {
                    return $this->configuration;
                }
                break;
        }

        return [];
    }

    protected function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
