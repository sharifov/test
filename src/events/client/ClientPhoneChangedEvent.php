<?php

namespace src\events\client;

use common\models\ClientPhone;

class ClientPhoneChangedEvent implements ClientPhoneEventInterface
{
    private ClientPhone $clientPhone;

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
