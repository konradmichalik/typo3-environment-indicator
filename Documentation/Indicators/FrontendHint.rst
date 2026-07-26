..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-FrontendHint.png
    :alt: Frontend Hint Icon
    :width: 120px

..  _frontend-hint:

=======================
Frontend Hint
=======================

The frontend hint will show the current environment information and the website title as clickable note in the upper right corner.

..  figure:: /Images/frontend-hint.png
    :alt: Frontend hint
    :class: with-shadow

    Frontend hint

You can adjust the color of the hint in your :code:`ext_localconf.php`:

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
            new Indicator\Frontend\Hint([
                'color' => '#bd593a',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The text of the hint. Default is the website title.
- :code:`position` (string): The position of the frontend hint. Default is :code:`top left`. Possible values are :code:`bottom left`,:code:`bottom right`, :code:`top left`, :code:`top right`.
- :code:`description` (string): An optional description shown below the hint text. Plain text only. Default is empty.
