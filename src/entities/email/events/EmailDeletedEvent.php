<?php

namespace src\entities\email\events;

use src\entities\email\EmailBody;

/**
 * Class EmailDeletedEvent
 *
 * @property EmailBody $emailBody
 * @property int $emailId
 */
class EmailDeletedEvent
{
    public EmailBody $emailBody;
    public int $emailId;

    /**
     * @property EmailBody $emailBody
     */
    public function __construct(EmailBody $emailBody, int $emailId)
    {
        $this->emailBody = $emailBody;
        $this->emailId = $emailId;
    }
}
