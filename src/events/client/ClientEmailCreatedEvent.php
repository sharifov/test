<?php

namespace src\events\client;

use common\models\ClientEmail;

class ClientEmailCreatedEvent
{
    public ClientEmail $clientEmail;

    /**
     * @param ClientEmail $clientEmail
     */
    public function __construct(ClientEmail $clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }
}
