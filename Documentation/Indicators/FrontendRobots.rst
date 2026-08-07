..  include:: /Includes.rst.txt

..  _frontend-robots:

===============
Frontend Robots
===============

Accidentally indexed staging environments are a common and expensive problem:
duplicate content competing with the live site, internal drafts surfacing in
search results, and clean-up work that takes weeks. This indicator signals the
environment to crawlers so non-production content does not end up in search
engines in the first place.

It adds an :code:`X-Robots-Tag` header to every frontend response:

..  code-block:: text

    X-Robots-Tag: noindex, nofollow

You can register the indicator in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger;

    Handler::addIndicator(
        triggers: [
            new Trigger\ApplicationContext('Testing/Staging')
        ],
        indicators: [
            new Indicator\Frontend\Robots()
        ]
    );

Additional optional configuration keys:

- :code:`content` (string): The header value. Default is
  :code:`noindex, nofollow`. A value that is not valid per :rfc:`9110` is
  ignored and logged as a warning.

..  warning::
    This indicator changes how search engines treat the site. Never trigger it
    in a production context — a :code:`noindex` on the live site removes it
    from search results. Restrict it to non-production contexts via its
    triggers and double-check the trigger configuration before deploying.

..  note::
    Unlike the other indicators, this one is **not** part of the default
    configuration presets and never will be. Silently changing the SEO
    behaviour of an existing installation on update would be unacceptable, so
    the indicator only ever does something if you register it yourself.

..  note::
    The :code:`X-Robots-Tag` header is used instead of a
    :code:`<meta name="robots">` tag on purpose: unlike the meta tag it also
    covers non-HTML resources such as PDFs, images and feeds, which are
    otherwise indexed even on a staging system.

..  note::
    An :code:`X-Robots-Tag` that is already present on the response keeps
    precedence — the indicator never overwrites it. The same header handling
    rules as for :ref:`frontend-http-header` apply.

The :code:`X-Robots-Tag` header can be disabled globally via the
:ref:`extension configuration <extconf-frontend.robots>`.
