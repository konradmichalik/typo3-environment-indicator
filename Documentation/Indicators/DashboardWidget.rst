..  include:: /Includes.rst.txt

..  _dashboard-widget:

=======================
Dashboard Widget
=======================

The dashboard widget can be placed to the TYPO3 backend dashboard. It shows the current environment regarding the configuration.

..  figure:: /Images/dashboard-widget.jpg
    :alt: Dashboard Widget Example

    Dashboard Widget Example

..  figure:: /Images/widget-selection.jpg
    :alt: Dashboard Widget Selection

    Dashboard Widget Selection

The widget is registered with its own icon (identifier :code:`environment-indicator-widget`), which is shown in the widget selection dialog:

..  image:: /Images/environment-indicator-widget.svg
    :alt: Environment Indicator Widget Icon
    :width: 32px

You can adjust the widget configuration in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development/Text')
        ],
        indicators: [
            new Indicator\Backend\Widget([
                'color' => '#bd593a',
            ])
        ]
    );


Additional optional configuration keys:

- :code:`text` (string): The text of the widget. Default is the application context.
- :code:`icon` (string): The icon of the widget. Default is :code:`information-application-context`.
- :code:`textSize` (string): The text size of the widget. Default is :code:`20px`.

The dashboard widget can not disappear, if no configuration is set. It is always shown in the backend dashboard.

..  figure:: /Images/dashboard-widget-production.jpg
    :alt: Dashboard Widget Production Example

    Dashboard Widget Production Example
