<?php declare(strict_types=1);
/**
 * @copyright Zicht Online <https://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Symfony\Component\Translation\TranslatorInterface;

class DummyTranslator implements TranslatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }

    /**
     * {@inheritDoc}
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }

    /**
     * {@inheritDoc}
     */
    public function setLocale($locale)
    {
        // Stub
    }

    /**
     * {@inheritDoc}
     */
    public function getLocale()
    {
        return 'en';
    }
}
