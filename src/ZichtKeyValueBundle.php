<?php

namespace Zicht\Bundle\KeyValueBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zicht\Bundle\KeyValueBundle\DependencyInjection\CompilerPass\KeysDefinerCompilerPass;

class ZichtKeyValueBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new KeysDefinerCompilerPass());
    }
}
