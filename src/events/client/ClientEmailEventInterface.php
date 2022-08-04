<?php

namespace src\events\client;

use common\models\ClientEmail;

interface ClientEmailEventInterface
{
    public function getClientEmail(): ClientEmail;
}
