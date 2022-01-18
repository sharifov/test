<?php

namespace modules\user\userFeedback;

use modules\user\userFeedback\entity\UserFeedback;

class UserFeedbackRepository
{
    public function save(UserFeedback $userFeedback): int
    {
        if (!$userFeedback->save()) {
            throw new \RuntimeException('UserFeedback saving failed: ' . $userFeedback->getErrorSummary(true)[0]);
        }
        return $userFeedback->uf_id;
    }
}
