<?php

namespace src\repositories\email;

use src\repositories\NotFoundException;
use common\models\Email;
use src\dispatchers\EventDispatcher;
use yii\db\Expression;

class EmailOldRepository implements EmailRepositoryInterface
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Email
    {
        if ($email = Email::findOne($id)) {
            return $email;
        }
        throw new NotFoundException('Email not found. ID: ' . $id);
    }

    public function save($email): int
    {
        if (!$email->save()) {
            throw new \RuntimeException('Email save failed: ' . $email->getErrorSummary(true)[0]);
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $email->e_id;
    }

    public function read($email): void
    {
        if ($email->e_is_new === true) {
            $email->updateAttributes([
                'e_is_new' => false,
                'e_read_dt' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function delete($email): int
    {
        $id = $email->e_id;
        if ($email->delete() === false) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $id;
    }

    public function deleteByIds($id): array
    {
        $removedIds = [];
        foreach (Email::findAll(['e_id' => $id]) as $model) {
            $removedIds[] = $this->delete($model);
        }
        return $removedIds;
    }

    public function getCommunicationLogQueryForLead(int $leadId)
    {
        return Email::find()
            ->lead($leadId)
            ->select(['e_id AS id', new Expression('"email" AS type'), 'e_lead_id AS lead_id', 'e_created_dt AS created_dt']);
    }
}
