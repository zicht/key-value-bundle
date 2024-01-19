<?php
/**
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Bundle\KeyValueBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\LocaleDependentData;

class LocalizationListener
{
    /** @var string[] */
    private $locales;

    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->isMasterRequest()) {
            $locale = $event->getRequest()->getLocale();

            // Fallback to first defined locale when the supplied locale is unknown
            if (!in_array($locale, $this->locales)) {
                $locale = current($this->locales);
            }

            LocaleDependentData::setLocale($locale);
        }
    }
}
