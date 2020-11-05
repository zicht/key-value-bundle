<?php declare(strict_types=1);
/**
 * @copyright Zicht Online <https://www.zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\KeyValueStorage;

use Symfony\Component\Translation\TranslatorInterface;

class DummyTranslator implements TranslatorInterface
{
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }

    public function setLocale($locale)
    {
        // Stub
    }

    public function getLocale()
    {
        return 'en';
    }
}
