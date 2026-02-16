..  include:: /Includes.rst.txt

..  _trigger-backend-user-groups:

============
Backend User Group
============

The :code:`BackendUserGroup` trigger is used to show the indicators in the TYPO3 backend for specific backend user groups.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\BackendUserGroup(1,2)
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

The configuration supports multiple group IDs as separate arguments. The trigger passes if the current backend user is a member of **any** of the specified groups (OR logic).
