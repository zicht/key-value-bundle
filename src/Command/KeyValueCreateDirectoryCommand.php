<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyValueCreateDirectoryCommand extends ContainerAwareCommand
{
    /** @var string */
    protected static $defaultName = 'zicht:key-value:create-directory';

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(
                [
                    new InputArgument('target', InputArgument::OPTIONAL, 'The target directory', 'web'),
                ]
            )
            ->setDescription('Ensure that the `web/media/key_value_bundle` exists');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targetArg = rtrim($input->getArgument('target'), '/');
        if (!is_dir($targetArg)) {
            throw new \InvalidArgumentException(sprintf('The target directory "%s" does not exist.', $input->getArgument('target')));
        }

        $filesystem = $this->getContainer()->get('filesystem');
        $filesystem->mkdir($targetArg . '/media/key_value_bundle/', 0777);
    }
}
