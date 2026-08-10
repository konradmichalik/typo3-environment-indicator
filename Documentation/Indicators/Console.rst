..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BrowserConsole.png
    :alt: Console Icon
    :width: 120px

..  _console:

=======
Console
=======

Developers usually have the browser console open anyway. This indicator prints
a styled environment badge to the console on every page load — more subtle than
the visible :ref:`frontend-hint`, but unambiguous for the "developer view", and
it never covers page content.

..  figure:: /Images/preview-console.png
    :alt: Environment badge printed to the browser console

    Console badge

..  code-block:: text

    DEVELOPMENT

The badge is printed in the **frontend and in the backend**, so the environment
stays identifiable while working in a module as well.

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
            new Indicator\General\Console([
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
    Both scopes are rendered from the same source, so the badge cannot look
    different in the frontend than in the backend. Only the delivery differs:
    the frontend uses an external JavaScript file that reads the badge from
    data attributes, the backend an inline script registered through
    :php:`PageRenderer`.

..  note::
    Both work under an enforced, nonce-based Content Security Policy. The
    frontend script tag carries a CSP nonce, and the backend — which enforces
    a nonce-based policy by default — gets its nonce from :php:`PageRenderer`.

..  note::
    A percent sign in the badge text is escaped automatically. Without that,
    the browser console would read it as a format directive and drop the
    styling of everything after it — relevant when using
    :ref:`instance.label <extconf-instance.label>` values such as
    :code:`TEST 100%`.

The console badge can be disabled per scope via the extension configuration:
:ref:`frontend.console <extconf-frontend.console>` and
:ref:`backend.console <extconf-backend.console>`.
