<?php

namespace src\model\emailReviewQueue;

use common\models\Email;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use src\model\emailReviewQueue\entity\EmailReviewQueueRepository;
use src\entities\email\Email as EmailNorm;

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

    public function createByEmail($email, ?int $departmentId): EmailReviewQueue
    {
        $model = EmailReviewQueue::create(
            $email->e_id,
            $email->e_project_id,
            $departmentId,
            $email->e_created_user_id,
            ($email instanceof EmailNorm)
        );
        $model->statusToPending();
        $this->repository->save($model);
        return $model;
    }
}
