<?php

use modules\flight\src\entities\flight\events\FlightChangedDelayedChargeEvent;
use modules\flight\src\entities\flight\events\FlightChangedStopsEvent;
use modules\flight\src\events\FlightRequestUpdateEvent;
use modules\flight\src\listeners\FlightRequestUpdateEventListener;

return [
    FlightRequestUpdateEvent::class => [FlightRequestUpdateEventListener::class],
    FlightChangedStopsEvent::class => [],
    FlightChangedDelayedChargeEvent::class => [],
];
