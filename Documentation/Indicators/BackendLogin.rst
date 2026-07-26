..  include:: /Includes.rst.txt

..  _backend-login:

===================
Backend Login
===================

All other backend indicators only take effect *after* login (topbar, toolbar,
logo, theme). Yet the moment with the highest risk of confusion is the login
itself — anyone who accidentally logs into live instead of staging only notices
afterwards. The backend login indicator closes this gap by showing a colored
environment badge directly on the backend login screen.

The badge is injected via a listener on the
:php:`ModifyPageLayoutOnLoginProviderSelectionEvent`. It is a fixed-position
banner (top or bottom), so it is independent of the login form markup and works
with alternative login providers (e.g. OIDC/SSO extensions).

You can register the indicator in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Production/Staging')
        ],
        indicators: [
            new Indicator\Backend\Login([
                'text' => 'Staging',
                'color' => '#00ACC1',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`text` (string): The badge text. Default is the current application
  context.
- :code:`color` (string): The badge background color as a hex value. Default is
  :code:`#bd593a`. The text color is calculated automatically for optimal
  contrast.
- :code:`position` (string): The badge position, :code:`top` or :code:`bottom`.
  Default is :code:`top`.
- :code:`description` (string): An optional descriptive line shown below the
  badge text (e.g. "Data is overwritten nightly from live").

..  note::
    There is no backend user yet on the login screen, so the :ref:`trigger-admin`
    and :ref:`trigger-backend-user-groups` triggers cannot resolve and are
    treated as "not fulfilled" — a configuration gated by them will not activate
    on the login screen. :ref:`trigger-application-context`, :ref:`trigger-ip`
    and :ref:`trigger-custom` work as usual.

The backend login badge can be disabled globally via the
:ref:`extension configuration <extconf-backend.login>`.
