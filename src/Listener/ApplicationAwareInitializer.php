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

namespace Behat\LaravelExtension\Listener;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
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

    /**
     * @var array
     */
    private $supportedBootTraits = [
        'Illuminate\Foundation\Testing\RefreshDatabase' => 'refreshDatabase',
        'Illuminate\Foundation\Testing\DatabaseMigrations' => 'runDatabaseMigrations',
        'Illuminate\Foundation\Testing\DatabaseTransactions' => 'beginDatabaseTransactions',
        'Illuminate\Foundation\Testing\WithoutMiddleware' => 'disableMiddlewareForAllTests',
        'Illuminate\Foundation\Testing\WithoutEvents' => 'disableEventsForAllTests',
    ];

    private $bootedTraits = [];

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
        $uses = array_flip(class_uses_recursive(\get_class($context)));

        foreach ($this->supportedBootTraits as $name => $method) {
            if (isset($uses[$name])) {
                $this->bootTrait($context, $name, $method);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ScenarioTested::BEFORE => ['handleScenarioBefore', -15],
        ];
    }

    public function handleScenarioBefore()
    {
        $this->bootedTraits = [];
    }

    private function bootTrait(ApplicationAwareContract $context, $name, $method)
    {
        if (\in_array($name, $this->bootedTraits, true)) {
            return;
        }
        $callback = [$context, $method];
        \call_user_func($callback);
        $this->bootedTraits[] = $name;
    }
}
