<?php
/**
 * @copyright Zicht Online <http://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

interface PredefinedKeyInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getFriendlyName();

    /**
     * @return string
     */
    public function getFormType();

    /**
     * @return array
     */
    public function getFormOptions();
}
