..  include:: /Includes.rst.txt

..  _extension-configuration:

=======================
Extension configuration
=======================

#. Go to :guilabel:`Admin Tools > Settings > Extension Configuration`
#. Choose :guilabel:`typo3_environment_indicator`

The extension currently provides the following configuration options:

General
=======

..  _extconf-general.imageDriver:

..  confval:: general.imageDriver
    :type: option
    :Default: "gd"

    Intervention Image supports "GD Library", "Imagick" and "libvips" to process images internally. You may choose one of them according to your PHP configuration. Options are "gd", "imagick" and "vips".

..  _extconf-general.defaultConfiguration:

..  confval:: general.defaultConfiguration
    :type: boolean
    :Default: 1

    Enable the default configuration for the environment indicators. If you want to configure the environment indicators by yourself, set this to false. See :ref:`configuration`.

Frontend
========

..  _extconf-frontend.favicon:

..  confval:: frontend.favicon
    :type: boolean
    :Default: 1

    Enable the favicon generation in frontend context

..  _extconf-frontend.context:

..  confval:: frontend.context
    :type: boolean
    :Default: 1

    Enable the context frontend hint

..  _extconf-frontend.image:

..  confval:: frontend.image
    :type: boolean
    :Default: 1

    Enable the image generation in frontend context

..  _extconf-frontend.pageTitle:

..  confval:: frontend.pageTitle
    :type: boolean
    :Default: 1

    Enable the page title prefix/suffix in frontend context. When the
    :ref:`page-title` indicator is active, the final rendered :code:`<title>`
    is decorated.

..  _extconf-frontend.header:

..  confval:: frontend.header
    :type: boolean
    :Default: 1

    Enable the environment HTTP response header in frontend context. When the
    :ref:`frontend-http-header` indicator is active, the configured header is
    added to frontend responses.

..  _extconf-frontend.robots:

..  confval:: frontend.robots
    :type: boolean
    :Default: 1

    Enable the :code:`X-Robots-Tag` response header in frontend context. This
    is a kill switch only — the :ref:`frontend-robots` indicator is never part
    of the default configuration and has to be registered explicitly, so
    nothing is sent to crawlers unless you asked for it.

..  _extconf-frontend.console:

..  confval:: frontend.console
    :type: boolean
    :Default: 1

    Enable the environment badge in the browser console. When the
    :ref:`frontend-console` indicator is active, a styled badge is printed to
    the console on every frontend page load.

Backend
=======

..  _extconf-backend.favicon:

..  confval:: backend.favicon
    :type: boolean
    :Default: 1

    Enable the favicon generation in backend context

..  _extconf-backend.logo:

..  confval:: backend.logo
    :type: boolean
    :Default: 1

    Enable the logo generation in backend context

..  _extconf-backend.context:

..  confval:: backend.context
    :type: boolean
    :Default: 1

    Enable the context item within the backend toolbar

..  _extconf-backend.contextProduction:

..  confval:: backend.contextProduction
    :type: boolean
    :Default: 1

    Enable the backend toolbar item / backend topbar also in production context

..  _extconf-backend.theme:

..  confval:: backend.theme
    :type: boolean
    :Default: 0

    Enable the backend theme modification (TYPO3 v14+ only, experimental).
    When the :ref:`backend-theme` indicator is active, this colors the entire
    backend based on the environment. Disabled by default because the
    underlying TYPO3 backend CSS framework is currently considered internal
    API.

..  _extconf-backend.login:

..  confval:: backend.login
    :type: boolean
    :Default: 1

    Enable the environment badge on the backend login screen. When the
    :ref:`backend-login` indicator is active, a colored badge is shown on the
    login screen.

..  _extconf-backend.pageTitle:

..  confval:: backend.pageTitle
    :type: boolean
    :Default: 1

    Enable the page title prefix/suffix in backend context. When the
    :ref:`page-title` indicator is active, the backend document title is
    decorated client-side.

CLI
===

..  _extconf-cli.banner:

..  confval:: cli.banner
    :type: boolean
    :Default: 0

    Enable the CLI banner (opt-in, disabled by default). When the
    :ref:`cli-banner` indicator is active, a colored environment banner is
    printed to :code:`stderr` before console commands run in an interactive
    terminal.

Mail
====

..  _extconf-mail.subject:

..  confval:: mail.subject
    :type: boolean
    :Default: 1

    Enable the mail subject prefix for outgoing mails. When the
    :ref:`mail-subject-prefix` indicator is active, the environment prefix is
    prepended to the subject of every mail sent via the TYPO3 Mailer API.

Instance
========

The instance settings allow overriding the *appearance* of all active
indicators for a single installation — directly from the backend, without any
PHP configuration. They are stored in the extension configuration of the
respective installation, so two systems sharing the same code base and
application context (e.g. two testing systems) can be distinguished from each
other.

Instance settings take precedence over the built-in presets *and* any
programmatically registered configuration. Which indicators are active remains
trigger-driven: on a system where no indicator resolves (e.g. production with
default configuration), the instance settings have no effect.

..  _extconf-instance.label:

..  confval:: instance.label
    :type: string
    :Default: (empty)

    Overrides the indicator text of all active indicators for this
    installation (e.g. "TEST 1"). Replaces the text of the backend toolbar
    item, dashboard widget and console badge and renders the label onto the
    favicon and backend logo. Leave empty to use the context defaults.

..  _extconf-instance.color:

..  confval:: instance.color
    :type: color
    :Default: (empty)

    Overrides the indicator color of all active indicators for this
    installation (frontend hint, console badge, backend toolbar, topbar,
    dashboard widget, backend theme and — combined with a label —
    favicon/logo). Leave empty to use the context defaults.
