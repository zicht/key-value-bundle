<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zicht\Bundle\KeyValueBundle\DependencyInjection\CompilerPass\KeysDefinerCompilerPass;

/**
 * Bundle instance for zicht/key-value-bundle
 */
class ZichtKeyValueBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new KeysDefinerCompilerPass());
    }
}
