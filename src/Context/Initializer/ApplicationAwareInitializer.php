<?php

/*
 * This file is part of the Behat\LaravelExtension project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Behat\LaravelExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\LaravelExtension\Contracts\ApplicationAwareContract;
use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApplicationAwareInitializer implements ContextInitializer, EventSubscriberInterface
{
    /**
     * @var LaravelFactoryContract
     */
    private $appFactory;

    public function __construct(
        LaravelFactoryContract $appFactory
    ) {
        $this->appFactory = $appFactory;
    }

    public function initializeContext(Context $context)
    {
        if (!$context instanceof ApplicationAwareContract) {
            return;
        }

        $context->setApplication($this->appFactory->getApplication());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::AFTER => ['rebootApplication', -15],
            ExampleTested::AFTER => ['rebootApplication', -15],
        ];
    }

    public function rebootApplication()
    {
        $this->appFactory->reboot();
    }
}
