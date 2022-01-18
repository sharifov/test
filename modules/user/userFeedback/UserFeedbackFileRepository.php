<?php

namespace modules\user\userFeedback;

use modules\user\userFeedback\entity\UserFeedbackFile;

class UserFeedbackFileRepository
{
    public function save(UserFeedbackFile $userFeedbackFile): int
    {
        if (!$userFeedbackFile->save()) {
            throw new \RuntimeException('UserFeedbackFile saving failed: ' . $userFeedbackFile->getErrorSummary(true)[0]);
        }
        return $userFeedbackFile->uff_id;
    }
}
