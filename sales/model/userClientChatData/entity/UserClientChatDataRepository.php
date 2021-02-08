<?php

namespace sales\model\userClientChatData\entity;

/**
 * Class UserClientChatDataRepository
 */
class UserClientChatDataRepository
{
    public function save(UserClientChatData $clientChatCase): void
    {
        if (!$clientChatCase->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }
}
