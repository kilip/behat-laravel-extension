<?php

namespace Behat\LaravelExtension\ServiceContainer;

use Behat\LaravelExtension\ApplicationConfigurator;
use Behat\LaravelExtension\PackageConfigurator;
use Behat\LaravelExtension\ServiceContainer\Driver\LaravelFactory;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Illuminate\Foundation\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LaravelExtension implements ExtensionInterface
{
    const KERNEL_SERVICE_ID = 'laravel.app';

    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }

    public function getConfigKey()
    {
        return 'laravel';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
        if(null !== $minkExtension = $extensionManager->getExtension('mink')){
            $minkExtension->registerDriverFactory(new LaravelFactory());
        }
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->enumNode('type')
                    ->values(['application','package'])
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
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter('laravel.config.providers', $config['providers']);
        $container->setParameter('laravel.config.aliases', $config['aliases']);
        $container->setParameter('laravel.config.environment', $config['environment']);

        $this->setupConfigurator($container, $config);

        $this->setupApplicationFactory($container, $config);

    }

    private function setupApplicationFactory(ContainerBuilder $container, array $config)
    {
        $configuratorId = 'laravel.configurator.application';
        if('package' === $config['type']){
            $configuratorId = 'laravel.configurator.package';
        }
        $container->setAlias('laravel.configurator', $configuratorId);

        $definition = new Definition(Application::class);
        $definition->setFactory(new Reference('laravel.configurator'));
        $container->setDefinition('laravel.app', $definition);
    }

    private function setupConfigurator(ContainerBuilder $container, array $config)
    {
        $package = new Definition(PackageConfigurator::class,[
            '%laravel.config.providers%',
            '%laravel.config.aliases%',
            '%laravel.config.environment%'
        ]);
        $package->addMethodCall('boot');
        $container->setDefinition('laravel.configurator.package',$package);

        $app = new Definition(ApplicationConfigurator::class);
        $package->addMethodCall('boot');
        $container->setDefinition('laravel.configurator.application', $app);
    }

}
