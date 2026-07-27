..  include:: /Includes.rst.txt

..  image:: /Images/Extension-EI-BackendLogin.png
    :alt: Backend Login Icon
    :width: 120px

..  _backend-login:

===================
Backend Login
===================

All other backend indicators only take effect *after* login (topbar, toolbar,
logo, theme). Yet the moment with the highest risk of confusion is the login
itself — anyone who accidentally logs into live instead of staging only notices
afterwards. The backend login indicator closes this gap by showing a colored
environment badge directly on the backend login screen.

..  figure:: /Images/backend-login.jpg
    :alt: Backend Login Example

    Backend Login Example

The badge is injected via a listener on the
:php:`ModifyPageLayoutOnLoginProviderSelectionEvent`. It is attached directly to
the login card (above the logo, or below the card footer), matching the card's
rounded corners and font. If the login card markup is not found — e.g. with an
alternative login provider (OIDC/SSO extensions) — it falls back to a
full-width banner at the top or bottom of the page.

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
