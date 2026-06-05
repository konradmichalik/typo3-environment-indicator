..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BackendTopbar.png
    :alt: Backend Topbar Icon
    :width: 120px

..  _backend-topbar:

=======================
Backend topbar
=======================

The backend toolbar item will show the current project version and environment.

..  figure:: /Images/backend-topbar.jpg
    :alt: Backend topbar
    :class: with-shadow

    Backend topbar

You can adjust the color of the topbar in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Testing')
        ],
        indicators: [
            new Indicator\Backend\Topbar([
                'color' => '#bd593a',
            ])
        ]
    );

..  note::
    The backend topbar is a feature, which can also be shown in production environments. Use the :ref:`extension settings <extconf-backend.contextProduction>` to enable, disable or restrict it.
