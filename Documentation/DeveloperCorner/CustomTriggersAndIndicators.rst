..  include:: /Includes.rst.txt

..  _custom-triggers-and-indicators:

=================================
Custom triggers and indicators
=================================

Beyond the built-in triggers and indicators, you can create your own implementations to match custom conditions or render custom indicators.

.. contents:: Table of Contents
   :local:
   :depth: 2

Custom triggers
=================

A trigger determines whether a set of indicators should be activated. Implement the :code:`TriggerInterface` with a single :code:`check()` method that returns a boolean value.

..  code-block:: php
    :caption: Classes/Configuration/Trigger/MyCustomTrigger.php

    <?php

    namespace Vendor\YourExt\Configuration\Trigger;

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Trigger\TriggerInterface;

    class MyCustomTrigger implements TriggerInterface
    {
        public function __construct(
            private readonly string $expectedValue,
        ) {}

        public function check(): bool
        {
            return getenv('MY_ENV_VAR') === $this->expectedValue;
        }
    }

Then use it in your :code:`ext_localconf.php`:

..  code-block:: php
    :caption: ext_localconf.php

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Handler;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator;
    use Vendor\YourExt\Configuration\Trigger\MyCustomTrigger;

    Handler::addIndicator(
        triggers: [
            new MyCustomTrigger('production-cluster-a'),
        ],
        indicators: [
            new Indicator\Backend\Topbar([
                'color' => '#e74c3c',
            ]),
        ],
    );

..  note::
    When multiple triggers are passed to a single :code:`Handler::addIndicator()` call, **all** triggers must return :code:`true` (AND logic) for the indicators to activate. If any trigger throws an exception, it is treated as :code:`false`.

..  seealso::

    View the sources on GitHub:

    -   `TriggerInterface <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Configuration/Trigger/TriggerInterface.php>`__

Custom indicators
==================

An indicator defines what is rendered when its associated triggers pass. Implement the :code:`IndicatorInterface` or extend :code:`AbstractIndicator`.

..  code-block:: php
    :caption: Classes/Configuration/Indicator/MyCustomIndicator.php

    <?php

    namespace Vendor\YourExt\Configuration\Indicator;

    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\AbstractIndicator;
    use KonradMichalik\Typo3EnvironmentIndicator\Configuration\Indicator\IndicatorInterface;

    class MyCustomIndicator extends AbstractIndicator implements IndicatorInterface
    {
        public function __construct(array $configuration = [])
        {
            parent::__construct($configuration);
        }

        public function getConfiguration(): array
        {
            return $this->configuration;
        }
    }

The :code:`AbstractIndicator` base class automatically merges global default configuration from
:code:`$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['typo3_environment_indicator']['defaults']` if a matching entry for the indicator class exists.

..  seealso::

    View the sources on GitHub:

    -   `IndicatorInterface <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Configuration/Indicator/IndicatorInterface.php>`__
    -   `AbstractIndicator <https://github.com/konradmichalik/typo3-environment-indicator/blob/main/Classes/Configuration/Indicator/AbstractIndicator.php>`__
