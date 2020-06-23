<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration schema for the key-value-bundle.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('zicht_key_value');
        $rootNode = $treeBuilder->getRootNode();

        // @formatter:off
        $rootNode
            ->children()
                ->arrayNode('json_defaults')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('defaults')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('locales')
                    ->info('A list with locales (and their CMS labels) used by the LocaleDependentDataType')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('locale')->isRequired()->end()
                            ->scalarNode('label')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('paths')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('web')
                            ->info('Publicly accessible directory of the webroot, default `/public`')
                            ->defaultValue('/public')
                        ->end()
                        ->scalarNode('storage')
                            ->info('Publicly accesible directory where the key-value files are stored, default `/public/media/key_value_storage`')
                            ->defaultValue('/public/media/key_value_storage')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache')
                    ->canBeEnabled()
                    ->children()
                        ->enumNode('type')->values(['service'])->end()
                        ->scalarNode('id')->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
