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

    public function getLanguageId(): ?string;

    public function getTemplateType();

    public function hasLead(): bool;

    public function hasCase(): bool;

    public function getLead();

    public function getCase();
}
