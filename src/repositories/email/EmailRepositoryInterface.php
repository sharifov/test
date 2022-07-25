<?php

namespace src\repositories\email;

use src\entities\email\Email;
use src\entities\email\EmailInterface;

interface EmailRepositoryInterface
{
    public function find(int $id): EmailInterface;

    public function save(EmailInterface $email);

    public function read(EmailInterface $email): void;

    public function delete(EmailInterface $email): int;

    public function deleteByIds($id): array;

    public function getCommunicationLogQueryForLead(int $leadId);

    public function getCommunicationLogQueryForCase(int $caseId);
}
