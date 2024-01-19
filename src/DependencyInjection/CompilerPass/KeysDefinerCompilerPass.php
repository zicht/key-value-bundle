<?php

namespace Zicht\Bundle\KeyValueBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class KeysDefinerCompilerPass implements CompilerPassInterface
{
    /** Finds services tagged with `zicht_bundle_key_value.keys_definer` and adds them to the StorageManager. */
    public function process(ContainerBuilder $container): void
    {
        // merge possibly multiple config yml files into one $defaults array
        $defaults = [];
        foreach ($container->getExtensionConfig('zicht_key_value') as $config) {
            if (array_key_exists('json_defaults', $config)) {
                $defaults = array_merge($defaults, array_map([$this, 'jsonDecode'], $config['json_defaults']));
            }
            if (array_key_exists('defaults', $config)) {
                $defaults = array_merge($defaults, $config['defaults']);
            }
            if (array_key_exists('cache', $config) && 'service' === $config['cache']['type']) {
                $cacheService = new Reference($config['cache']['id']);
                $container->getDefinition('zicht_bundle_key_value.key_value_storage_manager')
                    ->replaceArgument(3, $cacheService);
            }
        }

        $definition = $container->getDefinition('zicht_bundle_key_value.key_value_storage_manager');
        $taggedServices = $container->findTaggedServiceIds('zicht_bundle_key_value.keys_definer');
        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->addMethodCall('setDefaultValues', [$defaults]);
            $definition->addMethodCall('addKeysDefiner', [new Reference($id)]);
        }

        if (!$container->getParameter('kernel.debug')) {
            $container->removeDefinition('Zicht\Bundle\KeyValueBundle\KeyValueStorage\KeyValueStorageManagerDebugWrapper');
        }
    }

    /**
     * Decode the json encoded string
     *
     * @param string $value
     * @return mixed
     */
    private function jsonDecode($value)
    {
        return json_decode($value, true);
    }
}
