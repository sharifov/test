<?php

namespace sales\model\emailReviewQueue;

use common\models\Email;
use sales\model\emailReviewQueue\entity\EmailReviewQueue;
use sales\model\emailReviewQueue\entity\EmailReviewQueueRepository;

/**
 * @property EmailReviewQueueRepository $repository
 */
class EmailReviewQueueManageService
{
    private EmailReviewQueueRepository $repository;

    public function __construct(EmailReviewQueueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createByEmail(Email $email, ?int $departmentId): EmailReviewQueue
    {
        $model = EmailReviewQueue::create(
            $email->e_id,
            $email->e_project_id,
            $departmentId,
            $email->e_created_user_id
        );
        $model->statusToPending();
        $this->repository->save($model);
        return $model;
    }
}
