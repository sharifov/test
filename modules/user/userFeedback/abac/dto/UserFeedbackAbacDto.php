<?php

namespace modules\user\userFeedback\abac\dto;

use modules\user\userFeedback\entity\UserFeedback;

class UserFeedbackAbacDto extends \StdClass
{
    public ?UserFeedback $userFeedback;

    public bool $is_owner = false;

    public function __construct(?UserFeedback $userFeedback = null, ?int $userId = null)
    {
        $this->userFeedback = $userFeedback;
        if ($userFeedback && $userId) {
            $this->is_owner = $userFeedback->isOwner($userId);
        }
    }
}
