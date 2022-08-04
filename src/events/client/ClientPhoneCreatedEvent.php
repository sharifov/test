<?php

namespace src\events\client;

use common\models\ClientPhone;

class ClientPhoneCreatedEvent implements ClientPhoneEventInterface
{
    public ClientPhone $clientPhone;

    /**
     * @param ClientPhone $clientPhone
     */
    public function __construct(ClientPhone $clientPhone)
    {
        $this->clientPhone = $clientPhone;
    }

    public function getClientPhone(): ClientPhone
    {
        return $this->clientPhone;
    }
}
