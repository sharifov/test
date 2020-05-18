<?php

namespace modules\email\src;

/**
 * Class Result
 *
 * @property array $emailsTo
 */
class Result
{
    public $emailsTo;

    public function __construct(array $emailsTo = [])
    {
        $this->emailsTo = $emailsTo;
    }
}
