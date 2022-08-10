<?php

namespace src\exception;

use src\helpers\email\MaskEmailHelper;

class EmailNotSentException extends \RuntimeException
{
    protected $emailTo;

    /**
     * @param string $emailTo
     * @param string $message
     */
    public function __construct($emailTo, $message = '')
    {
        $this->emailTo = $emailTo;
        parent::__construct($message);
    }

    public function getEmailTo($mask = true): string
    {
        return $mask ? MaskEmailHelper::masking($this->emailTo) : $this->emailTo;
    }
}
