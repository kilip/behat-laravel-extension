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
use Behat\LaravelExtension\Factory\LaravelAppTypeFactory;
use Behat\LaravelExtension\Factory\LaravelPackageTypeFactory;
use Behat\LaravelExtension\ServiceContainer\Driver\LaravelFactory;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Illuminate\Foundation\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LaravelExtension implements ExtensionInterface
{
    public const KERNEL_SERVICE_ID = 'laravel.app';

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
            $minkExtension->registerDriverFactory(new LaravelFactory());
        }
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('bootstrap_file')
                    ->defaultValue('bootstrap/app.php')
                ->end()
                ->enumNode('type')
                    ->values(['application', 'package'])
                    ->defaultValue('application')
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
                            ->then(function($v){
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

        $this->loadFactories($container, $config);
        $this->loadApplicationFactory($container, $config);
        $this->loadContextInitializer($container, $config);
    }

    private function loadApplicationFactory(ContainerBuilder $container, array $config)
    {
        $configuratorId = 'laravel.factory.application';
        if ('package' === $config['type']) {
            $configuratorId = 'laravel.factory.package';
        }
        $container->setAlias('laravel.factory', $configuratorId);
        $container->setAlias(LaravelFactoryContract::class, $configuratorId);

        $definition = new Definition(Application::class);
        $definition->setFactory([new Reference('laravel.factory'), 'getApplication']);
        $container->setDefinition('laravel.app', $definition);
    }

    private function loadFactories(ContainerBuilder $container, array $config)
    {
        $package = new Definition(LaravelPackageTypeFactory::class, [
            '%laravel.config.providers%',
            '%laravel.config.aliases%',
            '%laravel.config.environment%',
        ]);
        $package->addMethodCall('boot');
        $container->setDefinition('laravel.factory.package', $package);

        $app = new Definition(LaravelAppTypeFactory::class, [
            '%laravel.config.bootstrap_file%',
        ]);
        $package->addMethodCall('boot');
        $container->setDefinition('laravel.factory.application', $app);
    }

    private function loadContextInitializer(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Behat\LaravelExtension\Context\Initializer\ApplicationAwareInitializer', [
            new Reference('laravel.factory'),
        ]);

        $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, ['priority' => 0]);
        $container->setDefinition('laravel.context.app_initializer', $definition);
    }

    /**
     * @TODO: perform tests for this functionality
     * @param array $config
     * @return array
     */
    private function normalizeConfig(array $config)
    {
        $environment = $config['environment'];
        $prefix = 'serialized:';
        foreach($environment as $key => $value){
            if(substr($value,0,strlen($prefix)) !==  $prefix){
                continue;
            }
            $value = str_replace($prefix,'', $value);
            $config['environment'][$key] = unserialize($value);
        }
        return $config;
    }
}
