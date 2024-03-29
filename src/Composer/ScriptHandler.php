<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\Composer;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

class ScriptHandler extends BaseScriptHandler
{
    /**
     * Calls the zicht:key-value:create-directory console command
     */
    public static function createKeyValueStorageDirectory(Event $event)
    {
        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, 'create directory');

        if (null === $consoleDir) {
            return;
        }

        $webDir = $options['symfony-web-dir'];

        if (!static::hasDirectory($event, 'symfony-web-dir', $webDir, 'create directory')) {
            return;
        }

        static::executeCommand($event, $consoleDir, 'zicht:key-value:create-directory ' . escapeshellarg($webDir), $options['process-timeout']);
    }
}
