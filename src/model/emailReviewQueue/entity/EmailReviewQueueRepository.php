<?php

namespace src\model\emailReviewQueue\entity;

class EmailReviewQueueRepository
{
    public function save(EmailReviewQueue $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException('EmailReviewQueue saving failed: ' . $model->getErrorSummary(true)[0]);
        }
        return $model->erq_id;
    }
}
