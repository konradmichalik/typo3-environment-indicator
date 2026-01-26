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

namespace KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

use function in_array;

/**
 * FrontendUserGroup.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class FrontendUserGroup implements TriggerInterface
{
    /**
     * @var array<int, int>
     */
    protected array $groups;

    public function __construct(int ...$group)
    {
        $this->groups = array_values($group);
    }

    public function check(): bool
    {
        $request = $this->getRequest();
        if (null === $request) {
            return false;
        }

        /** @var FrontendUserAuthentication|null $frontendUser */
        $frontendUser = $request->getAttribute('frontend.user');
        if (null === $frontendUser) {
            return false;
        }

        $currentUserGroups = $frontendUser->groupData['uid'] ?? [];
        foreach ($this->groups as $group) {
            if (in_array($group, $currentUserGroups, true)) {
                return true;
            }
        }

        return false;
    }

    protected function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
