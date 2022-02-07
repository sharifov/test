<?php

namespace modules\user\userFeedback;

use modules\user\userFeedback\entity\UserFeedback;

class UserFeedbackRepository
{
    public function save(UserFeedback $userFeedback, int $attempts = 0): int
    {
        try {
            if (!$userFeedback->save()) {
                throw new \RuntimeException('UserFeedback saving failed: ' . $userFeedback->getErrorSummary(true)[0]);
            }
            return $userFeedback->uf_id;
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), "no partition of relation")) {
                $dates = UserFeedback::partitionDatesFrom(date_create_from_format('Y-m-d H:i:s', $userFeedback->uf_created_dt));
                UserFeedback::createMonthlyPartition($dates[0], $dates[1]);

                if ($attempts > 0) {
                    throw new \RuntimeException("unable to create user+feedback partition");
                }

                $this->save($userFeedback, ++$attempts);
            }
            throw $e;
        }
    }
}
