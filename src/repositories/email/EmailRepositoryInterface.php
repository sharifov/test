<?php

namespace src\repositories\email;

use src\entities\email\Email;
use src\entities\email\EmailInterface;
use yii\db\ActiveQuery;

interface EmailRepositoryInterface
{
    public function find(int $id): EmailInterface;

    public function save(EmailInterface $email);

    public function delete(EmailInterface $email): int;

    public function deleteByIds($id): array;

    public function getCommunicationLogQueryForLead(int $leadId);

    public function getCommunicationLogQueryForCase(int $caseId);

    public function getTodayCount(int $cache);

    public function findReceived(string $messageId, string $emailTo): ActiveQuery;

    public function saveInboxId(EmailInterface $email, int $inboxId): void;

    public function getLastInboxId(): ?int;

    public function getEmailCountByLead(int $leadId, $cache = 0): int;

    public function getEmailCountByCase(int $caseId, $cache = 0): int;

    public function getEmailCountForLead(int $leadId, int $type = 0): int;

    public function getModelQuery(): ActiveQuery;

    public function getTableName(): string;

    public function getRawSqlCountGroupedByLead(): string;

    public function getRawSqlCountGroupedByCase(): string;

    public function getQueryLastEmailByCase(int $caseId, int $type): ActiveQuery;

    public function getSubQueryLeadEmailOffer(): ActiveQuery;

    public function getCasesByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery;

    public function getCasesCreatorByEmailsToAndCreated($emailsTo, string $createdDate): ActiveQuery;

    public function getStatsData(string $startDate, string $endDate, int $type);
}
