<?php

namespace modules\cases\src\abac\dto;

use sales\auth\Auth;
use sales\entities\cases\Cases;

/**
 * @property bool $is_owner
 */
class CasesAbacDto extends \stdClass
{
    public bool $is_owner;
    public ?int $status_id = null;

    public bool $to_pending = true;
    public bool $to_processing = true;
    public bool $to_follow_up = true;
    public bool $to_solved = true;
    public bool $to_trash = true;
    public bool $to_new = true;
    public bool $to_awaiting = true;
    public bool $to_auto_processing = true;
    public bool $to_error = true;

    public function __construct(?Cases $case)
    {
        if ($case) {
            $this->is_owner = $case->isOwner(Auth::id());
            $this->status_id = $case->cs_status;
        }
    }
}
