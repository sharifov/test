<?php

namespace src\repositories\email;

use src\entities\email\Email;
use src\entities\email\EmailInterface;
use yii\db\ActiveQuery;

interface EmailRepositoryInterface
{
    public function find(int $id): EmailInterface;

    public function save(EmailInterface $email);

    public function read(EmailInterface $email): void;

    public function delete(EmailInterface $email): int;

    public function deleteByIds($id): array;

    public function getCommunicationLogQueryForLead(int $leadId);

    public function getCommunicationLogQueryForCase(int $caseId);

    public function getTodayCount(int $cache);

    public function findReceived(string $messageId, string $emailTo): ActiveQuery;

    public function saveInboxId(EmailInterface $email, int $inboxId): void;

    public function getLastInboxId(): ?int;
}
