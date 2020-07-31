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

namespace spec\Behat\LaravelExtension\Listener;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\LaravelExtension\Contracts\ApplicationAwareContract;
use Behat\LaravelExtension\Contracts\ApplicationAwareTrait;
use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Behat\LaravelExtension\Listener\ApplicationAwareInitializer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Webmozart\Assert\Assert;

class ApplicationAwareInitializerSpec extends ObjectBehavior
{
    public function let(LaravelFactoryContract $factory, Application $application)
    {
        $factory->getApplication()->willReturn($application);
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApplicationAwareInitializer::class);
    }

    public function it_should_be_a_context_initializer()
    {
        $this->shouldImplement(ContextInitializer::class);
    }

    public function it_should_subscribe_to_behat_events()
    {
        $events = ApplicationAwareInitializer::getSubscribedEvents();

        Assert::keyExists($events, ScenarioTested::BEFORE);
    }

    public function it_should_boot_used_traits_in_context(
        TestContext $testContext,
        $application
    ) {
        $this->configureContextExpectation($testContext, $application);
        $testContext->refreshDatabase()
            ->shouldBeCalledOnce();
        $this->initializeContext($testContext);
    }

    public function it_should_boot_traits_once_only(
        $application,
        TestContext $testContext,
        TestMultiContext $testMultiContext
    ) {
        $testContext->refreshDatabase()
            ->shouldBeCalledOnce();
        $testMultiContext->runDatabaseMigrations()
            ->shouldBeCalledOnce();
        $this->configureContextExpectation($testContext, $application);
        $this->configureContextExpectation($testMultiContext, $application);

        $this->initializeContext($testContext);
        $this->initializeContext($testMultiContext);
    }

    private function configureContextExpectation(ApplicationAwareContract $context, $application)
    {
        $context->setApplication($application)->shouldBeCalledOnce();
    }

    public function it_should_handle_before_scenario_event(
        TestContext $testContext
    ) {
        $testContext
            ->setApplication(Argument::type(Application::class))
            ->shouldBeCalled();
        $testContext->refreshDatabase()->shouldBeCalledTimes(2);

        // refreshDatabase should be called once only
        $this->initializeContext($testContext);
        $this->initializeContext($testContext);

        // refreshDatabase should be called after bootedTraits reset
        $this->handleScenarioBefore();
        $this->initializeContext($testContext);
    }
}

class TestContext implements Context, ApplicationAwareContract
{
    use ApplicationAwareTrait;
    use RefreshDatabase;
}

class TestMultiContext implements Context, ApplicationAwareContract
{
    use ApplicationAwareTrait;
    use RefreshDatabase;
    use DatabaseMigrations;
}
