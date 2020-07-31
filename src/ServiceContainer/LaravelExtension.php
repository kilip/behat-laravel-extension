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

namespace Behat\LaravelExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Behat\LaravelExtension\Factory\LaravelFactory;
use Behat\LaravelExtension\ServiceContainer\Driver\DriverFactory;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LaravelExtension implements ExtensionInterface
{
    public function process(ContainerBuilder $container)
    {
    }

    public function getConfigKey()
    {
        return 'laravel';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new DriverFactory());
        }
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('bootstrap_file')
                    ->defaultValue($this->detectDefaultBootstrapFile())
                ->end()
                ->arrayNode('providers')
                    ->info('list your package providers')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('aliases')
                    ->info('list your package aliases')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('environment')
                    ->info('setup your configuration')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
                        ->beforeNormalization()
                            ->ifArray()
                            ->then(function ($v) {
                                return 'serialized:'.serialize($v);
                            })
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $config = $this->normalizeConfig($config);
        $container->setParameter('laravel.config.bootstrap_file', $config['bootstrap_file']);
        $container->setParameter('laravel.config.providers', $config['providers']);
        $container->setParameter('laravel.config.aliases', $config['aliases']);
        $container->setParameter('laravel.config.environment', $config['environment']);

        $this->loadFactories($container);
        $this->loadContextInitializer($container);
    }

    private function loadFactories(ContainerBuilder $container)
    {
        $appFactory = new Definition(LaravelFactory::class, [
            '%laravel.config.bootstrap_file%',
            '%laravel.config.providers%',
            '%laravel.config.aliases%',
            '%laravel.config.environment%',
        ]);
        $container->setDefinition('laravel.factory.app', $appFactory);
        $container->setAlias(LaravelFactoryContract::class, 'laravel.factory.app');

        $driverFactory = new Definition(DriverFactory::class, [new Reference(ContainerInterface::class)]);
        $container->setDefinition('laravel.factory.driver', $driverFactory);
    }

    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('Behat\LaravelExtension\Listener\ApplicationAwareInitializer', [
            new Reference('laravel.factory.app'),
        ]);

        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition('laravel.context.app_initializer', $definition);
    }

    /**
     * @TODO: perform tests for this functionality
     *
     * @param array $config
     *
     * @return array
     */
    private function normalizeConfig(array $config)
    {
        $environment = $config['environment'];
        $prefix = 'serialized:';
        foreach ($environment as $key => $value) {
            if (substr($value, 0, \strlen($prefix)) !== $prefix) {
                continue;
            }
            $value = str_replace($prefix, '', $value);
            $config['environment'][$key] = unserialize($value);
        }

        return $config;
    }

    private function detectDefaultBootstrapFile()
    {
        $bootstrapFile = 'bootstrap/app.php';
        if (!is_file($bootstrapFile)) {
            $bootstrapFile = __DIR__.'/../../../../../bootstrap/app.php';
        }

        if (!is_file($bootstrapFile)) {
            $bootstrapFile = __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';
        }

        return realpath($bootstrapFile);
    }
}
