<?php

namespace src\model\client\entity\events;

use common\models\Client;

/**
 * Interface ClientEventInterface
 */
interface ClientEventInterface
{
    public function getClient(): Client;
}
