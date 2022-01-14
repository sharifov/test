<?php

namespace src\dispatchers;

interface EventDispatcher
{
    public function dispatchAll(array $events): void;
    public function dispatch($event, $key = null): void;
}
