<?php

namespace src\entities\email;

use src\repositories\NotFoundException;
use src\dispatchers\EventDispatcher;

class EmailRepository
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
        if ($email->emailLog && $email->emailLog->el_is_new === true) {
            $email->saveEmailLog([
                'el_is_new' => false,
                'el_read_dt' => date('Y-m-d H:i:s')
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

    /**
     * @param array $mailList
     * @return int
     */
    public function getUnreadCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->unread()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getInboxTodayCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->inbox()->createdToday()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getOutboxTodayCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->outbox()->createdToday()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getDraftCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->notDeleted()->draft()->count();
    }

    /**
     * @param array $mailList
     * @return int
     */
    public function getTrashCount(array $mailList): int
    {
        return Email::find()->withContact($mailList)->deleted()->count();
    }
}
