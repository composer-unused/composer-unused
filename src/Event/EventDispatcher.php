<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    /** @var iterable|ListenerProviderInterface[] */
    private $listenerProviders;

    /**
     * @param ListenerProviderInterface[] $listenerProviders
     */
    public function __construct(iterable $listenerProviders = [])
    {
        $this->listenerProviders = $listenerProviders;
    }

    public function dispatch(object $event): object
    {
        foreach ($this->listenerProviders as $listenerProvider) {
            foreach ($listenerProvider->getListenersForEvent($event) as $listener) {
                if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                    return $event;
                }

                $listener($event);
            }
        }

        return $event;
    }
}
