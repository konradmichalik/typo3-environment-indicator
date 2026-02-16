..  include:: /Includes.rst.txt

..  _trigger-frontend-user-groups:

============
Frontend User Group
============

The :code:`FrontendUserGroup` trigger is used to show the indicators in the TYPO3 Frontend for specific frontend user groups.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\FrontendUserGroup(1,2)
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

The configuration supports multiple group IDs as separate arguments. The trigger passes if the current frontend user is a member of **any** of the specified groups (OR logic). If no frontend user session is available, the trigger returns :code:`false`.
