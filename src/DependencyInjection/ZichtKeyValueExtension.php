<?php

namespace Zicht\Bundle\KeyValueBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ZichtKeyValueExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('form_types.xml');
        $loader->load('services.xml');

        $projectDir = $container->getParameter('kernel.project_dir');
        $container->getDefinition('zicht_bundle_key_value.key_value_storage_manager')
            ->replaceArgument(1, sprintf('%s..%s', $projectDir, $config['paths']['web']))
            ->replaceArgument(2, sprintf('%s..%s', $projectDir, $config['paths']['storage']));

        $container->getDefinition('zicht_bundle_key_value.form_type.locale_dependent_data_type')
            ->replaceArgument(0, $config['locales']);

        $container->getDefinition('zicht_bundle_key_value.event_listener.localization_listener')
            ->replaceArgument(0, array_map(fn ($value) => $value['locale'], $config['locales']));
    }
}
