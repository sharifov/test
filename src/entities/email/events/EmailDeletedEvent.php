<?php

namespace src\entities\email\events;

use src\entities\email\EmailBody;

/**
 * Class EmailDeletedEvent
 *
 * @property EmailBody $emailBody
 */
class EmailDeletedEvent
{
    public $emailBody;

    /**
     * @property EmailBody $emailBody
     */
    public function __construct(EmailBody $emailBody)
    {
        $this->emailBody = $emailBody;
    }
}
