<?php

namespace src\entities\email;

interface EmailInterface
{
    public function getProjectId(): ?int;

    public function getDepartmentId(): ?int;

    public function getTemplateTypeId(): ?int;

    public function getLeadId(): ?int;

    public function getCaseId(): ?int;

    public function getClientId(): ?int;

    public function getEmailFrom($masking = true): ?string;

    public function getEmailTo($masking = true): ?string;

    public function getEmailFromName(): ?string;

    public function getEmailToName(): ?string;

    public function getLanguageId(): ?string;

    public function getTemplateType();

    public function hasLead(): bool;

    public function hasCase(): bool;

    public function hasClient(): bool;

    public function getLead();

    public function getCase();

    public function getClient();

    public function getProject();

    public function getStatusDoneDt();

    public function getErrorMessage();

    public function getCommunicationId();

    public function getCreatedUser();

    public function isCreatedUser(int $userId): bool;

    public function hasCreatedUser(): bool;

    public function getStatusName(): string;

    public function getTypeName(): string;
}
