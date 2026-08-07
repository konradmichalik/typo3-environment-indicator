..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BrowserConsole.png
    :alt: Frontend Console Icon
    :width: 120px

..  _frontend-console:

================
Frontend Console
================

Developers usually have the browser console open anyway. This indicator prints
a styled environment badge to the console on every page load — more subtle than
the visible :ref:`frontend-hint`, but unambiguous for the "developer view", and
it never covers page content.

..  code-block:: text

    DEVELOPMENT

You can register the indicator in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Development*')
        ],
        indicators: [
            new Indicator\Frontend\Console([
                'color' => '#bd593a',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The badge text. Default is :code:`%context%`, which is
  replaced with the current application context. An empty text disables the
  badge.
- :code:`color` (string): The badge background color. Default is
  :code:`#767676`. The text color is derived automatically for optimal
  contrast.

..  note::
    The indicator is part of the default configuration presets for the
    non-production contexts, using the same colors as the :ref:`frontend-hint`.

..  note::
    The badge is printed by a regular external JavaScript file that reads the
    text and colors from data attributes. No inline script is involved, so the
    indicator needs no CSP nonce and works unchanged on installations with an
    enforced Content Security Policy.

..  note::
    A percent sign in the badge text is escaped automatically. Without that,
    the browser console would read it as a format directive and drop the
    styling of everything after it — relevant when using
    :ref:`instance.label <extconf-instance.label>` values such as
    :code:`TEST 100%`.

The console badge can be disabled globally via the
:ref:`extension configuration <extconf-frontend.console>`.
