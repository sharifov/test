<?php

namespace sales\services\lead\calculator;

/**
 * Class SegmentDTO
 *
 * @property $origin
 * @property $destination
 * @property $departure
 */
class SegmentDTO
{

    public $origin;

    public $destination;

    public $departure;

    /**
     * @param string|null $origin
     * @param string|null $destination
     * @param string|null $departure
     */
    public function __construct(?string $origin, ?string $destination, ?string $departure = '')
    {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->departure = $departure;
    }

}
