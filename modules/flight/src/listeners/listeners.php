<?php

use modules\flight\src\events\FlightRequestUpdateEvent;
use modules\flight\src\listeners\FlightRequestUpdateEventListener;

return [
    FlightRequestUpdateEvent::class => [FlightRequestUpdateEventListener::class],
];
