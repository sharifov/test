<?php

namespace modules\flight\src\repositories\flightQuoteOption;

use modules\flight\src\entities\flightQuoteOption\FlightQuoteOption;

class FlightQuoteOptionRepository
{
    public function save(FlightQuoteOption $option): int
    {
        if ($option->save(false)) {
            return $option->fqo_id;
        }
        throw new \RuntimeException('Flight quote option saving failed');
    }
}
