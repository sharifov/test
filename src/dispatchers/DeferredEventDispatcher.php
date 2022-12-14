<?php

namespace src\dispatchers;

class DeferredEventDispatcher implements EventDispatcher
{
    private $defer = false;
    private $queue = [];
    private $next;

    public function __construct(EventDispatcher $next)
    {
        $this->next = $next;
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    public function dispatch($event, $key = null): void
    {
        if ($this->defer) {
            if ($key) {
                $this->queue[$key] = $event;
            } else {
                $this->queue[] = $event;
            }
        } else {
            $this->next->dispatch($event);
        }
    }

    public function defer(): void
    {
        $this->defer = true;
    }

    public function clean(): void
    {
        $this->queue = [];
        $this->defer = false;
    }

    public function detachByKey(string $key): void
    {
        if (isset($this->queue[$key])) {
            unset($this->queue[$key]);
        }
    }

    public function release(): void
    {
        foreach ($this->queue as $i => $event) {
            $this->next->dispatch($event);
            unset($this->queue[$i]);

            if (property_exists($event, 'resetDispatcherQueue') && $event->resetDispatcherQueue) {
                reset($this->queue);
                $this->release();
            }
        }
        $this->defer = false;
    }
}
