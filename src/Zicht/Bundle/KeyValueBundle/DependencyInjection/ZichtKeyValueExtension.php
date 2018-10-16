<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ZichtKeyValueExtension.
 */
class ZichtKeyValueExtension extends Extension
{
    /**
     * Responds to the twig configuration parameter.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('admin.xml');
        $loader->load('form_types.xml');
        $loader->load('services.xml');

        $rootDir = $container->getParameter('kernel.root_dir');
        $container->getDefinition('zicht_bundle_key_value.key_value_storage_manager')
            ->replaceArgument(1, realpath(sprintf('%s/%s', $rootDir, '../web')))
            ->replaceArgument(2, $this->checkDirectory(sprintf('%s/%s', $rootDir, '../web/media/key_value_storage')));

        $container->getDefinition('zicht_bundle_key_value.form_type.locale_dependent_data_type')
            ->replaceArgument(0, $config['locales']);

        $container->getDefinition('zicht_bundle_key_value.event_listener.localization_listener')
            ->replaceArgument(0, array_map(function ($value) { return $value['locale']; }, $config['locales']));
    }

    /**
     * Check, at compile time, if the directories are available and writable
     *
     * @param string $directory
     * @return string
     * @throws InvalidConfigurationException
     */
    private function checkDirectory($directory)
    {
        if (!is_dir($directory)) {
            @mkdir($directory);
        }

        $realDirectory = realpath($directory);
        if (false === $realDirectory) {
            throw new InvalidConfigurationException(sprintf('Directory does not exist [%s]', $directory));
        }

        if (!is_writeable($realDirectory)) {
            throw new InvalidConfigurationException(sprintf('Directory is not writable [%s]', $realDirectory));
        }

        return $realDirectory;
    }
}
