<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Event\Listener;

use Closure;
use Psr\EventDispatcher\ListenerProviderInterface;
use Traversable;
use Icanhazstring\Composer\Unused\Event\Exception\ListenerEventTypeResolveException;
use Icanhazstring\Composer\Unused\Event\ListenerEventTypeResolver;

class RuntimeListenerProvider implements ListenerProviderInterface
{
    /** @var array<string, array<string, callable>> */
    protected $listeners = [];
    /** @var ListenerEventTypeResolver */
    private $eventTypeResolver;

    public function __construct(ListenerEventTypeResolver $eventTypeResolver)
    {
        $this->eventTypeResolver = $eventTypeResolver;
    }

    /**
     * @param object|Closure $listener
     * @throws ListenerEventTypeResolveException
     */
    public function addListener($listener): void
    {
        $eventType = $this->eventTypeResolver->resolve($listener);
        $this->listeners[$eventType] = $this->listeners[$eventType] ?? [];
        $this->listeners[$eventType][spl_object_hash($listener)] = $listener;
    }

    /**
     * @param object|Closure $listener
     * @throws ListenerEventTypeResolveException
     */
    public function removeListener($listener): void
    {
        $eventType = $this->eventTypeResolver->resolve($listener);
        $listenerHash = spl_object_hash($listener);

        if (!isset($this->listeners[$eventType][$listenerHash])) {
            return;
        }

        unset($this->listeners[$eventType][$listenerHash]);
    }

    /**
     * @return Traversable<callable>
     */
    public function getListenersForEvent($event): Traversable
    {
        foreach ($this->listeners as $eventType => $listeners) {
            if (!$event instanceof $eventType) {
                continue;
            }

            foreach ($listeners as $listener) {
                yield $listener;
            }
        }
    }
}
