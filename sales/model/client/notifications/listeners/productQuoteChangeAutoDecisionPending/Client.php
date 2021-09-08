<?php

namespace sales\model\client\notifications\listeners\productQuoteChangeAutoDecisionPending;

/**
 * Class Client
 *
 * @property int $id
 * @property int|null $phoneId
 * @property int|null $emailId
 */
class Client
{
    public int $id;
    public ?int $phoneId;
    public ?int $emailId;

    public function __construct(int $id, ?int $phoneId, ?int $emailId)
    {
        $this->id = $id;
        $this->phoneId = $phoneId;
        $this->emailId = $emailId;
    }
}
