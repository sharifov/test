<?php

namespace webapi\src\response;

use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\StatusMessage;

trait ProcessStandardMessageTrait
{
    public function processStandardMessages(Message ...$messages): array
    {
        $messageExist = false;
        $statusExist = false;
        $statusCodeExist = false;

        foreach ($messages as $message) {

            if ($message->isMessage()) {
                $messageExist = true;
            }

            if ($message->isStatus()) {
                $statusExist = true;
            }

            if ($message->isStatusCode()) {
                $statusCodeExist = true;
                $this->setStatusCode((int)$message->getValue());
            }

        }

        if (!$messageExist) {
            $messages[] = new MessageMessage($this->getMessageDefault());
        }

        if (!$statusCodeExist) {
            $messages[] = new StatusCodeMessage($this->getStatusCodeDefault());
        }

        if (!$statusExist) {
            $messages[] = new StatusMessage($this->getStatusCode() ?: $this->getStatusCodeDefault());
        }

        return $messages;
    }
}
