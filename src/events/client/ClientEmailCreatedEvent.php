<?php

namespace src\events\client;

use common\models\ClientEmail;

class ClientEmailCreatedEvent implements ClientEmailEventInterface
{
    private ClientEmail $clientEmail;

    /**
     * @param ClientEmail $clientEmail
     */
    public function __construct(ClientEmail $clientEmail)
    {
        $this->clientEmail = $clientEmail;
    }

    public function getClientEmail(): ClientEmail
    {
        return $this->clientEmail;
    }
}
