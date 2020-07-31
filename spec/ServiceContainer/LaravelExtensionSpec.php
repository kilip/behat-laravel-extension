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

namespace spec\Behat\LaravelExtension\ServiceContainer;

use Behat\LaravelExtension\ServiceContainer\Driver\DriverFactory;
use Behat\LaravelExtension\ServiceContainer\LaravelExtension;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webmozart\Assert\Assert;

class LaravelExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelExtension::class);
    }

    public function it_should_be_a_behat_extension()
    {
        $this->shouldImplement(Extension::class);
    }

    public function it_should_returns_config_key()
    {
        $this->getConfigKey()->shouldReturn('laravel');
    }

    public function it_should_register_mink_driver_for_laravel(
        MinkExtension $mink
    ) {
        $mink->getConfigKey()->shouldBeCalled()->willReturn('mink');
        $mink->registerDriverFactory(Argument::type(DriverFactory::class))
            ->shouldBeCalled();

        $manager = new ExtensionManager([$mink->getWrappedObject()]);

        $this->initialize($manager);
    }

    public function it_should_define_configuration_with_default_values()
    {
        $config = $this->processConfig();
        Assert::fileExists($config['bootstrap_file']);
        Assert::isEmpty($config['providers']);
        Assert::isEmpty($config['aliases']);
        Assert::isEmpty($config['environment']);
    }

    public function it_should_normalize_environment_config()
    {
        $config = $this->processConfig($source = [
            'environment' => [
                'root' => [
                    'hello' => [
                        'world' => 'hello world',
                    ],
                    'foo' => [
                        'bar' => 'foo bar',
                    ],
                ],
            ],
        ]);

        $config = $this->processConfig($config);
        Assert::notEmpty($config['environment']);
        $env = unserialize(str_replace('serialized:', '', $config['environment']['root']));
        Assert::eq($env, $source['environment']['root']);
    }

    public function it_should_load_configuration()
    {
        $builder = $this->loadDefaultConfig();
        Assert::true($builder->hasParameter('laravel.config.bootstrap_file'));
        Assert::true($builder->hasParameter('laravel.config.providers'));
        Assert::true($builder->hasParameter('laravel.config.aliases'));
        Assert::true($builder->hasParameter('laravel.config.environment'));

        $env = $builder->getParameter('laravel.config.environment');

        Assert::eq($env['foo']['bar'], 'foo bar');
        Assert::eq($env['hello'], 'hello world');
    }

    public function it_should_configure_services()
    {
        $builder = $this->loadDefaultConfig();

        Assert::true($builder->has('laravel.factory.app'));
        Assert::true($builder->has('laravel.factory.driver'));
        Assert::true($builder->has('laravel.context.app_initializer'));
    }

    public function loadDefaultConfig()
    {
        $config = $this->processConfig([
            'environment' => [
                'hello' => 'hello world',
                'foo' => [
                    'bar' => 'foo bar',
                ],
            ],
        ]);
        $builder = new ContainerBuilder();

        $this->load($builder, $config);

        return $builder;
    }

    public function processConfig(array $config = [])
    {
        $builder = new ArrayNodeDefinition('laravel');
        $this->configure($builder);
        $processor = new Processor();

        return $processor->process($builder->getNode(), ['laravel' => $config]);
    }
}
