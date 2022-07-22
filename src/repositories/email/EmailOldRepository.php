<?php

namespace src\repositories\email;

use src\repositories\NotFoundException;
use common\models\Email;

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

    public function save(Email $email): int
    {
        if (!$email->save()) {
            throw new \RuntimeException('Email save failed: ' . $email->getErrorSummary(true)[0]);
        }
        $this->eventDispatcher->dispatchAll($email->releaseEvents());
        return $email->e_id;
    }

    public function read(Email $email): void
    {
        if ($email->e_is_new === true) {
            $email->updateAttributes([
                'e_is_new' => false,
                'e_read_dt' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function delete(Email $email): int
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
}
