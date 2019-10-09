<?php

namespace sales\services\lead\calculator;

/**
 * Class SegmentDTO
 *
 * @property $origin
 * @property $destination
 */
class SegmentDTO
{

    public $origin;

    public $destination;

    /**
     * @param string|null $origin
     * @param string|null $destination
     */
    public function __construct(?string $origin, ?string $destination)
    {
        $this->origin = $origin;
        $this->destination = $destination;
    }

}
