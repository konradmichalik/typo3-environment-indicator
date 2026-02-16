..  include:: /Includes.rst.txt

..  _trigger-ip:

============
IP Address
============

The :code:`IP` trigger is used to show the indicators in the TYPO3 backend for specific IP addresses.

..  code-block:: php
    :caption: ext_localconf.php

    \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler::addIndicator(
        triggers: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\Ip('127.0.0.1', '192.168.0.0/24')
        ],
        indicators: [
            new \KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\Backend\Topbar([
                'color' => '#00ACC1',
            ]),
        ]
    );

The configuration supports multiple IP addresses as separate arguments. Both IPv4 and IPv6 addresses are supported. CIDR range notation is also supported for both address families (e.g. :code:`192.168.0.0/24` or :code:`2001:db8::/32`). Invalid IP formats will throw an :code:`InvalidArgumentException` during construction.
