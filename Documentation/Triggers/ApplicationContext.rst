..  include:: /Includes.rst.txt

..  _trigger-application-context:

============
Application Context
============

The :code:`ApplicationContext` trigger is used to show the indicators in the TYPO3 backend for a specific application context.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\ApplicationContext('Development*', 'Testing')
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

The configuration supports multiple context names as separate arguments. The context names support glob-style wildcard patterns (using PHP's :code:`fnmatch()`). The :code:`*` wildcard matches any number of characters and the :code:`?` wildcard matches a single character. For example, :code:`Development*` will match all contexts that start with :code:`Development`, such as :code:`Development`, :code:`Development/Local`, :code:`Development/DDEV`, etc.
