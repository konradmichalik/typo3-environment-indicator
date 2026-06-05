..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BackendToolbarItem.png
    :alt: Backend Toolbar Item Icon
    :width: 120px

..  _backend-toolbar-item:

=======================
Backend toolbar item
=======================

The backend toolbar item will show the current project version and environment.

..  figure:: /Images/backend-toolbar-item.png
    :alt: Backend toolbar item
    :class: with-shadow

    Backend toolbar item

You can adjust the color of the toolbar item in your :code:`ext_localconf.php`:

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
            new Indicator\Backend\Toolbar([
                'color' => '#bd593a',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The text of the toolbar item. Default is the application context.
- :code:`icon` (array): The icon configuration of the toolbar item. Expects a nested array with a :code:`context` key. Default is :code:`['context' => 'information-application-context']`.
- :code:`index` (int): The positioning index of the toolbar item. Default is :code:`0`.

..  note::
    The backend toolbar item is a feature, which can also be shown in production context. Use the :ref:`extension settings <extconf-backend.contextProduction>` to enable, disable or restrict it.
