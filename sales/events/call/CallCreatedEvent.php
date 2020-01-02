<?php

namespace sales\events\call;

use common\models\Call;

/**
 * Class CallCreatedEvent
 *
 * @property Call $call
 */
class CallCreatedEvent
{
    public $call;

    /**
     * @param Call $call
     */
    public function __construct(Call $call)
    {
        $this->call = $call;
    }
}
