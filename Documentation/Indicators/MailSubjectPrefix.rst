..  include:: /Includes.rst.txt

..  _mail-subject-prefix:

===================
Mail Subject Prefix
===================

Test and staging systems regularly send real emails — form submissions,
workflow notifications, scheduler reports. Recipients often cannot tell whether
a mail originates from the live system or a test system. The mail subject prefix
indicator prepends the environment to the subject of every mail sent through the
TYPO3 Mailer API (e.g. :code:`[Staging] Your registration`), so the origin is
visible at first glance in every inbox.

The prefix is applied via a listener on the :php:`BeforeMailerSentMessageEvent`,
covering all mails sent via TYPO3's Mailer API (frontend forms, backend
notifications, scheduler tasks).

You can register the indicator in your :code:`ext_localconf.php`:

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
            new Indicator\Mail\SubjectPrefix([
                'prefix' => '[%context%] ',
            ])
        ]
    );

Additional optional configuration keys:

- :code:`prefix` (string): The prefix prepended to the mail subject. Default is
  :code:`[%context%] `. The placeholder :code:`%context%` is replaced with the
  current application context.
- :code:`header` (string): If set, an additional mail header with this name and
  the application context as value is added (e.g. :code:`X-Environment: Testing`)
  for filtering/tooling. Default is empty (no header is added).

..  note::
    The prefix is only applied once. When a message is processed multiple times
    (e.g. queued or retried), a subject that already starts with the resolved
    prefix is left untouched.

..  note::
    Mails are often sent in a CLI or scheduler context without a backend/frontend
    user or client IP. In these cases user- and IP-based triggers
    (:ref:`trigger-admin`, :ref:`trigger-backend-user-groups`,
    :ref:`trigger-frontend-user-groups`, :ref:`trigger-ip`) cannot resolve and
    the indicator will not activate. :ref:`trigger-application-context` and
    :ref:`trigger-custom` work as usual.

..  note::
    This indicator is deliberately *indicator only*. It does not redirect or
    intercept mails ("catch-all to a developer inbox") — that is guard
    functionality well covered by other tools (Mailpit, :code:`transport_spool`).

..  note::
    The Install Tool's "Test Mail Setup" (Admin Tools > Environment) does not
    apply this prefix. Direct Install Tool access (:code:`?__typo3_install`)
    runs TYPO3's failsafe bootstrap, which only loads packages marked as part
    of the minimal usable system — third-party extensions, including this one,
    are skipped entirely, so the listener never registers for that request. The
    prefix still applies normally to mails sent through the regular bootstrap
    (frontend forms, backend notifications, scheduler tasks). To verify the
    feature locally, trigger an actual application mail (e.g. a password reset)
    or run the extension's functional test suite instead.

The mail subject prefix can be disabled globally via the
:ref:`extension configuration <extconf-mail.subject>`.
