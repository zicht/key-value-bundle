<?php

namespace Zicht\Bundle\KeyValueBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Zicht\Bundle\KeyValueBundle\KeyValueStorage\LocaleDependentData;

class LocalizationListener
{
    /** @var string[] */
    private $locales;

    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public function onKernelRequest(RequestEvent $event)
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
