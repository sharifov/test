<?php

namespace src\events\client;

use common\models\ClientPhone;

interface ClientPhoneEventInterface
{
    public function getClientPhone(): ClientPhone;
}
