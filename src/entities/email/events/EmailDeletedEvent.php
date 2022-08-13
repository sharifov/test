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
    public $emailBody;
    public $emailId;

    /**
     * @property EmailBody $emailBody
     */
    public function __construct(EmailBody $emailBody, int $emailId)
    {
        $this->emailBody = $emailBody;
        $this->emailId = $emailId;
    }
}
