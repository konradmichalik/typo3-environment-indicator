
..  include:: /Includes.rst.txt

..  _trigger-custom:

============
Custom
============

The :code:`Custom` trigger is used to show the indicators in the TYPO3 backend for custom conditions.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Custom(
                function () {
                    return true;
                }
            ),
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Custom('\YourExtension\YourClass::yourMethod'),
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

The configuration supports a closure or a string reference, which is executed to determine if the indicator should be shown. The function should return a boolean value. Other callables, such as array callables or invokable objects, are not supported.

When using a string reference, it must follow the format :code:`ClassName::methodName` (without parentheses). The referenced class and method must exist and the method must be :code:`public` and :code:`static`. Otherwise the
constructor throws an :code:`InvalidArgumentException`. If the function throws an exception during execution, the
trigger returns :code:`false` and the exception is logged if a logger was passed as second argument.
