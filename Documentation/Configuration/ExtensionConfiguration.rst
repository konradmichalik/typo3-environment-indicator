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
=======

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

..  _extconf-backend.pageTitle:

..  confval:: backend.pageTitle
    :type: boolean
    :Default: 1

    Enable the page title prefix/suffix in backend context. When the
    :ref:`page-title` indicator is active, the backend document title is
    decorated client-side.

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
    item and dashboard widget and renders the label onto the favicon and
    backend logo. Leave empty to use the context defaults.

..  _extconf-instance.color:

..  confval:: instance.color
    :type: color
    :Default: (empty)

    Overrides the indicator color of all active indicators for this
    installation (frontend hint, backend toolbar, topbar, dashboard widget,
    backend theme and — combined with a label — favicon/logo). Leave empty to
    use the context defaults.
