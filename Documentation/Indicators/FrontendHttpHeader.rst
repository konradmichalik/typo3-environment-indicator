..  include:: /Includes.rst.txt

..  _frontend-http-header:

====================
Frontend HTTP Header
====================

Besides the visual indicators, an environment marker on HTTP level is useful for
the "developer view": debugging, :code:`curl` checks, uptime monitoring and
browser extensions can identify the environment without parsing the page.

The indicator adds a configurable header to every frontend response:

..  code-block:: text

    X-TYPO3-Environment: Development

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
            new Indicator\Frontend\HttpHeader()
        ]
    );

Additional optional configuration keys:

- :code:`name` (string): The header name. Default is
  :code:`X-TYPO3-Environment`.
- :code:`value` (string): The header value. Default is :code:`%context%`. The
  placeholder :code:`%context%` is replaced with the current application
  context.

A name or value that is not valid per :rfc:`9110` is ignored and logged as a
warning, so a misconfiguration cannot break frontend rendering.

..  note::
    The indicator only applies to frontend responses. Backend responses are
    rarely inspected via :code:`curl` or tooling and are left untouched.

..  note::
    A header that the application already set is never overwritten. This keeps
    the indicator from clobbering a header of the same name coming from the
    site configuration, a reverse proxy or another extension.

..  warning::
    The header discloses the application context to anyone requesting the site.
    This is uncritical as long as the indicator is restricted to non-production
    contexts via its triggers — which is the intended use case. Do not register
    it for production unless you deliberately want that information to be
    public.

The environment HTTP header can be disabled globally via the
:ref:`extension configuration <extconf-frontend.header>`.
