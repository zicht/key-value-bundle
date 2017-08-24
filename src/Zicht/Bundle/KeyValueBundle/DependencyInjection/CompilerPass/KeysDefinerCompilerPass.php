<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class KeysDefinerCompilerPass.
 */
class KeysDefinerCompilerPass implements CompilerPassInterface
{
    /**
     * Finds services tagged with `zicht_bundle_key_value.keys_definer` and adds them to the StorageManager.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // merge possibly multiple config yml files into one $defaults array
        $defaults = [];
        foreach ($container->getExtensionConfig('zicht_key_value') as $config) {
            if (array_key_exists('defaults', $config)) {
                $defaults = array_merge($defaults, $config['defaults']);
            }
        }

        $definition = $container->getDefinition('zicht_bundle_key_value.key_value_storage_manager');
        $taggedServices = $container->findTaggedServiceIds('zicht_bundle_key_value.keys_definer');
        foreach ($taggedServices as $id => $attributes) {
            $container->getDefinition($id)->addMethodCall('setDefaultValues', [$defaults]);
            $definition->addMethodCall('addKeysDefiner', array(new Reference($id)));
        }
    }
}
