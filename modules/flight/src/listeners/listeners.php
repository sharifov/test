<?php

use modules\flight\src\events\FlightCountPassengersChangedEvent;
use modules\flight\src\listeners\FlightCountPassengersChangedEventListener;

return [
    FlightCountPassengersChangedEvent::class => [FlightCountPassengersChangedEventListener::class],
];
