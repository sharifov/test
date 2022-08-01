<?php

namespace src\events\client;

use common\models\ClientPhone;

class ClientPhoneCreatedEvent
{
    public ClientPhone $clientPhone;

    /**
     * @param ClientPhone $clientPhone
     */
    public function __construct(ClientPhone $clientPhone)
    {
        $this->clientPhone = $clientPhone;
    }
}
